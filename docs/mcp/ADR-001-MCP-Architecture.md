# ADR-001: Architecture Strategy for ACT RDW Modular MCP Server (Revised)

**Status:** Accepted (Revised)  
**Date:** 2026-09-28  
**Context:** ACT RDW Enterprise CMS (Laravel 13, Filament 4, Custom Modular Monolith)  
**Primary AI Consumers:** Hermes (Nous Research) / Nexa AI  

---

## 1. Problem Statement

ACT RDW requires a secure, AI-native interface (Model Context Protocol - MCP) enabling autonomous agents (Hermes, Nexa) to discover, inspect, and safely mutate core domain resources (Products, Brands, Categories, Articles/News) using explicit, capability-governed tools.

The solution must strictly satisfy these constraints:
1. **Preserve Custom Modular Architecture:** No external module packages (e.g. 
widart/laravel-modules).
2. **Immutable REST API Contract:** All existing REST endpoints (/api/products, /api/news, /api/products/import, /api/news/import) must remain 100% backward-compatible in HTTP methods, URL paths, authentication (uth:api), request schemas, and JSON:API response structures.
3. **No Internal REST Self-Calls:** MCP tools must invoke shared domain application services directly within the Laravel process.
4. **AI-Native Protocol Compliance:** Modern Streamable HTTP transport without legacy dual-endpoint SSE overhead unless explicitly required by client testing.
5. **Reused Authentication:** Reuse the existing VerifyApiKey (X-API-KEY) infrastructure with capability-based authorization and mutation audit logging.

---

## 2. Evaluation of Architectural Options

### Option A: External Third-Party PHP MCP Package
* **Evaluation:**
  - Most community packages in the PHP ecosystem are desktop-oriented (STDIO transport for local Claude Desktop), unmaintained, or designed for Symfony/older Laravel versions.
  - No package currently provides native integration with Laravel 13, Filament 4, and custom non-package modular structures.
  - Adding unvetted external dependencies creates high risk of composer conflicts, version lockouts, and security liabilities.
* **Verdict:** **Rejected.**

### Option B: Native Custom Laravel MCP Subsystem (pp/MCP/)
* **Evaluation:**
  - MCP is an open specification built on JSON-RPC 2.0.
  - Implementing the protocol directly within pp/MCP/ enables:
    - Native Streamable HTTP transport over standard Laravel routes and controllers.
    - Protocol version negotiation supporting modern MCP client handshakes.
    - Direct integration with Laravel Validator, DTOs, and exception pipelines.
    - Zero external dependency bloat; 100% maintainable within the codebase.
    - Direct dependency injection of shared application services.
* **Verdict:** **ACCEPTED.**

### Option C: Standalone MCP Microservice / Sidecar
* **Evaluation:**
  - Introduces extra network hops, cross-service authentication, database synchronization or internal HTTP proxying, deployment overhead, and operational latency.
* **Verdict:** **Rejected.**

---

## 3. Protocol Versioning & Compatibility Strategy

### 3.1 Dynamic Protocol Version Negotiation
* **No Hard-Coded Version:** The server does not pin itself to a static date.
* **Negotiation Lifecycle:**
  1. The client sends an initialize JSON-RPC request containing its requested protocolVersion (e.g., 2024-11-05, 2025-03-26, 2025-06-18, or newer).
  2. The server evaluates the requested version against its supported protocol matrix and responds with the mutually supported version in InitializeResult.protocolVersion.
  3. For subsequent Streamable HTTP requests, the client transmits the negotiated version in the MCP-Protocol-Version HTTP header.
  4. The server validates the MCP-Protocol-Version header on incoming requests, returning a structured JSON-RPC error if unsupported.

---

## 4. Transport Strategy: Modern Streamable HTTP

### 4.1 Streamable HTTP as Primary Remote Transport
* **Unified Single Endpoint:** /api/mcp (HTTP POST).
* **Rationale:**
  - The MCP specification introduced Streamable HTTP as the modern standard for remote client-to-server communication, officially superseding the legacy dual-connection HTTP+SSE pattern.
  - **No Legacy Dual-Endpoint SSE:** Legacy HTTP+SSE (which required separate /sse for events and /messages for POST) is **not** implemented by default. It introduces stateful connection tracking and proxy/firewall friction that is unnecessary for remote request-response tool calls.
  - **Single-Endpoint Streaming Support:** If Hermes/Nexa requests a streamed response, Streamable HTTP natively supports streaming chunks (via chunked transfer or streaming JSON) directly over the single POST connection at /api/mcp.
  - Legacy /api/mcp/sse will **only** be added if real-world compatibility testing against Hermes/Nexa proves that the specific deployed agent runtime cannot support Streamable HTTP.

### 4.2 Transport Metadata & Headers
* Headers supported on /api/mcp:
  - X-API-KEY: Client authentication credential (validated by VerifyApiKey).
  - MCP-Protocol-Version: Declared protocol version for the session.
  - Content-Type: pplication/json.
  - Accept: pplication/json, text/event-stream.

---

## 5. Hermes / Nexa Compatibility Target & Evidence

### 5.1 Hermes Agent (Nous Research)
* **Architecture:** Python-based autonomous agent framework with native MCP client integration.
* **Transport Compatibility:** 
  - Supports standard HTTP POST JSON-RPC 2.0 endpoints with header-based authentication.
  - In remote tool configurations, Hermes connects to HTTPS endpoints passing custom authorization headers (X-API-KEY or Bearer tokens).
  - Handles JSON-RPC 2.0 methods: initialize, 
otifications/initialized, 	ools/list, and 	ools/call.

### 5.2 Nexa AI / Agentic Integrations
* **Architecture:** Enterprise agentic workflow orchestrator.
* **Transport Compatibility:**
  - Modern MCP client implementation communicating over unified HTTP POST endpoints.
  - Standard JSON-RPC 2.0 payload format with tool schema discovery via 	ools/list.

---

## 6. REST API Immutability & Regression Test Strategy

### 6.1 Principle of Absolute REST Stability
Refactoring controllers to extract business logic into domain services (ProductMutationService, NewsMutationService, etc.) is an **internal architectural change only**. 
The external HTTP contracts must remain completely identical:
- Endpoints: GET /api/products, GET /api/products/{slug}, POST /api/products/import, GET /api/news, GET /api/news/{slug}, POST /api/news/import.
- Authentication: uth:api (JWT Auth) for imports; public read for indexes.
- Response Contracts: Exact JSON:API resource envelope, attributes, relationships, pagination meta, and status codes (200, 201, 401, 404, 422, 500).

### 6.2 Expanded Pre-Refactoring REST Test Suite
Before modifying any code in Modules/ProductCatalog or Modules/News, an expanded regression test suite (	ests/Feature/RestApiComprehensiveRegressionTest.php) must be established to lock down:
1. GET /api/products — Filter by category, filter by brand, pagination limit, active-only scope.
2. GET /api/products/{slug} — Valid slug retrieval, 404 for invalid/inactive slug, relationship inclusion (rand, service, categories, solutions, seo).
3. POST /api/products/import — Missing JWT (401), validation failure (422), full product creation with brand/category/solution/SEO upsert, image handling, and idempotent updates (200/201).
4. GET /api/news — Filter by category, filter by tag, pagination, published-only status.
5. GET /api/news/{slug} — Valid slug retrieval, 404 for draft/missing news, SEO attributes.
6. POST /api/news/import — Missing JWT (401), validation failure (422), article creation with category/tag/SEO upsert (200/201).

This suite must execute and pass **both before and after** service extraction to mathematically prove zero behavioral regression.