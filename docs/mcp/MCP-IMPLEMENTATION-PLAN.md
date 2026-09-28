# MCP Implementation Plan & Roadmap

**Target Consumer:** Hermes / Nexa AI  
**Repository:** ACT RDW (Laravel 13 Custom Modular Monolith)  
**Strategy:** Analyze -> Design -> Refactor Domain Services -> Implement MCP Core -> Implement Module Tools -> Validate & Test  

---

## Phase Overview

| Phase | Description | Deliverables / Artifacts | Status |
| :--- | :--- | :--- | :--- |
| **Phase 0** | Repository & Module System Deep Audit | docs/mcp/MCP-ARCHITECTURE-AUDIT.md | **Completed** |
| **Phase 1** | Architectural Decision Record | docs/mcp/ADR-001-MCP-Architecture.md | **Completed** |
| **Phase 2** | Service Boundary & Refactoring Plan | docs/mcp/SERVICE-BOUNDARY-PLAN.md | **Completed** |
| **Phase 2.5** | Domain Service Extraction & REST Verification | Services in Modules/ProductCatalog & Modules/News, REST Regression Tests | **Next** |
| **Phase 3** | MCP Core Engine Scaffolding | pp/MCP/ (Protocol, Server, Registry, Middleware, Auth, Audit) | Pending |
| **Phase 4** | ProductCatalog MCP Tools | Modules/ProductCatalog/MCP/Tools/* | Pending |
| **Phase 5** | News MCP Tools | Modules/News/MCP/Tools/* | Pending |
| **Phase 6** | Security, Capability Authorization & Audit | Capability Matrix, Rate Limiting, Audit Logger | Pending |
| **Phase 7** | Hermes / Nexa Integration | Tool Discovery Spec, SSE/HTTP Endpoints, Integration Docs | Pending |
| **Phase 8** | Final Audit & End-to-End Test Suite | Unit/Feature Tests, REST Regression, docs/mcp/MCP-FINAL-AUDIT.md | Pending |

---

## Detailed Implementation Steps

### Phase 2.5: Domain Service Extraction
1. **ProductCatalog Services:**
   - Create Modules/ProductCatalog/Services/ProductQueryService.php.
   - Create Modules/ProductCatalog/Services/ProductMutationService.php.
   - Refactor ProductApiController.php to delegate index(), show(), and import() to these services.
   - Run 	ests/Feature/JsonApiResourceTest.php to prove 100% REST backward compatibility.
2. **News Services:**
   - Create Modules/News/Services/NewsQueryService.php.
   - Create Modules/News/Services/NewsMutationService.php.
   - Refactor NewsApiController.php to delegate index(), show(), and import().
   - Run 	ests/Feature/JsonApiResourceTest.php to ensure zero regression.

### Phase 3: MCP Core Engine (pp/MCP/)
1. **Contracts:**
   - McpToolInterface: getName(), getDescription(), getInputSchema(), getRequiredCapability(), execute(array , McpContext ).
2. **Protocol & Server:**
   - JSON-RPC 2.0 parser & serializer: handles initialize, 	ools/list, 	ools/call.
   - Streamable HTTP controller (pp/MCP/Server/McpHttpController.php).
   - Server-Sent Events controller (pp/MCP/Server/McpSseController.php).
3. **Registry:**
   - McpRegistry singleton: registers tools and outputs standardized MCP tool catalogues.
4. **Authorization & Audit:**
   - McpAuthorizer: maps client API key to permissions (product.read, product.create, product.update, product.delete, rticle.read, rticle.create, etc.).
   - McpAuditLogger: writes mutation logs capturing client identity, tool name, inputs, resource IDs, and timestamps.
5. **Routing & Middleware:**
   - Register /api/mcp and /api/mcp/sse in outes/api.php under 'api_key' middleware.

### Phase 4: ProductCatalog MCP Tools
1. Modules/ProductCatalog/MCP/Tools/ProductListTool.php (product.list)
2. Modules/ProductCatalog/MCP/Tools/ProductGetTool.php (product.get)
3. Modules/ProductCatalog/MCP/Tools/ProductCreateTool.php (product.create)
4. Modules/ProductCatalog/MCP/Tools/ProductUpdateTool.php (product.update)
5. Modules/ProductCatalog/MCP/Tools/ProductDeleteTool.php (product.delete)
6. Modules/ProductCatalog/MCP/Tools/BrandListTool.php (rand.list)
7. Modules/ProductCatalog/MCP/Tools/BrandGetTool.php (rand.get)
8. Modules/ProductCatalog/MCP/Tools/ProductCategoryListTool.php (product_category.list)
9. Modules/ProductCatalog/MCP/Tools/ProductCategoryGetTool.php (product_category.get)
10. Register tools inside ProductCatalogServiceProvider::boot().

### Phase 5: News MCP Tools
1. Modules/News/MCP/Tools/ArticleListTool.php (rticle.list)
2. Modules/News/MCP/Tools/ArticleGetTool.php (rticle.get)
3. Modules/News/MCP/Tools/ArticleCreateTool.php (rticle.create)
4. Modules/News/MCP/Tools/ArticleUpdateTool.php (rticle.update)
5. Modules/News/MCP/Tools/ArticleDeleteTool.php (rticle.delete)
6. Register tools inside NewsServiceProvider::boot().

### Phase 6: Automated Testing & Verification
1. Create 	ests/Feature/McpProtocolTest.php: verifies initialize, 	ools/list, schema validity.
2. Create 	ests/Feature/McpProductToolsTest.php: tests product.create, product.update, product.list, product.delete.
3. Create 	ests/Feature/McpNewsToolsTest.php: tests rticle.create, rticle.update, rticle.list, rticle.delete.
4. Create 	ests/Feature/McpSecurityTest.php: tests missing API key, invalid key, forbidden capabilities, delete restrictions.
5. Run full test suite including JsonApiResourceTest.