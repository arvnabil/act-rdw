# MCP Architecture Audit

## Executive Summary
This document analyzes the current ACT RDW Laravel 13 architecture to determine the optimal strategy for integrating a native Model Context Protocol (MCP) server. The goal is to provide an AI-native interface for agents like Hermes/Nexa while preserving the existing Domain-Driven Design (DDD) modular architecture and REST APIs.

## Current Architecture
The ACT RDW application uses a Modular architecture via `nwidart/laravel-modules`.
- **Framework:** Laravel 13.0
- **Modules:** Located in `Modules/`. Core modules identified include `ProductCatalog`, `News`, `Services`, `SEO`, `AI`, etc.
- **Routing:** Each module registers its own routes (e.g., `Modules/ProductCatalog/Routes/api.php`) via its ServiceProvider.

## Module Inventory
Identified modules include: AI, Analytics, CMS, Campaign, Clients, Events, FormBuilder, Menu, News, ProductCatalog, Projects, SEO, Search, Services, Settings, WhatsApp.

## REST API Inventory
- **ProductCatalog:**
  - `GET /api/products` (Read-only)
  - `GET /api/products/{slug}` (Read-only)
  - `POST /api/products/import` (Protected via `auth:api`, handles creation/updates)
- **News:**
  - `GET /api/news` (Read-only)
  - `GET /api/news/{slug}` (Read-only)
  - `POST /api/news/import` (Protected via `auth:api`, handles creation/updates)

## Service Layer Inventory
- **ProductCatalog:** Contains `ProductService` and `ProductRepository`. However, `ProductService` only handles read operations (`getIndexData`, `getDetailData`). Write operations (creation, updating, SEO syncing, image processing) bypass the service layer and are implemented directly inside `ProductApiController::import`.
- **News:** Does not have a `Services` folder. All read and write logic resides inside `NewsApiController`.

## Authentication Analysis
The application uses two distinct authentication mechanisms:
1. `auth:api` (JWT Auth via `tymon/jwt-auth`), used in existing module API routes.
2. `VerifyApiKey` (Custom API key middleware checking `X-API-KEY` header against `ApiKey` model from the `Settings` module, logging requests via `ApiLog`).

## Authorization Analysis
Currently, authorization for API writes relies primarily on having a valid JWT (`auth:api`). There is no explicit role-based or capability-based policy engine evident in the analyzed controllers, though standard Laravel Policies may exist. MCP will require a more granular capability-based authorization mapping.

## MCP Technology Evaluation
The PHP/Laravel ecosystem for MCP is still emerging. There are a few options:
1. **Third-party Packages (e.g., `php-mcp/mcp-server` or `metoro-io/mcp-php`):** May lack deep Laravel modular integration out of the box and might be overly generic.
2. **Native Custom Laravel Implementation:** Since MCP is built on JSON-RPC 2.0 (over STDIO or HTTP/SSE), building a custom, lightweight modular MCP Server in Laravel is highly feasible and aligns perfectly with the application.

## Recommended Architecture
Option A (Refactor to Services). Extract the heavy logic in `ProductApiController::import` and `NewsApiController::import` into `ProductImportService` and `NewsImportService` so both REST API and MCP can share it.

