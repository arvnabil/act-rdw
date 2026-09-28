# Service Boundary & Refactoring Plan

**Feature:** ACT RDW MCP Integration & Modular Service Layer  
**Scope:** Modules/ProductCatalog and Modules/News  
**Principles:** Domain-Driven Design, Zero REST Breakage, Shared Application Services, No Internal HTTP Calls  

---

## 1. Context & Motivation

Currently, write/mutation operations in both ProductCatalog and News are trapped directly inside API controller methods (ProductApiController::import and NewsApiController::import). 
Existing services (ProductService) are read-only and coupled to Inertia web frontend page payloads.

If MCP tools were implemented today, they would either have to:
1. Duplicate the 150+ lines of validation, brand/category resolution, media processing, SEO syncing, and transaction logic.
2. Make internal HTTP self-calls to POST /api/products/import (strictly forbidden by architecture rules).

To achieve ** One Domain Multiple Interfaces**, we must extract clean, reusable Domain Services that sit below both the REST API controllers and the MCP tool handlers.

---

## 2. ProductCatalog Domain Service Boundaries

### 2.1 Current Responsibilities Breakdown
- **ProductController (Web):** Formats Inertia props, breadcrumbs, and SEO resolver data.
- **ProductApiController (REST):**
  - index(): Queries is_active = true, eager loads relationships, filters by category/brand, returns ProductResource::collection.
  - show(): Finds product by slug, returns 
ew ProductResource.
  - import(): 
    - Database transaction management.
    - Model lookup (irstOrNew(['slug' => ])).
    - Attribute mapping & JSON casting (specs, eatures, 	ags).
    - Brand auto-resolution/creation (Brand::firstOrCreate).
    - Service auto-resolution/creation (Service::firstOrCreate).
    - Image download and WebP conversion via ImageHelper.
    - Category M2M syncing (ProductCategory::firstOrCreate + sync).
    - Solutions M2M syncing (ServiceSolution::whereIn + sync).
    - Polymorphic SEO metadata update ($product->seo()->updateOrCreate).
    - Solution brand synchronization ($product->syncBrandToSolutions()).
- **Product Model:** Houses relations, HasSeoMeta, and HasImageCleanup traits.

### 2.2 Target Service Architecture
We define three dedicated services under Modules/ProductCatalog/Services/:

1. **ProductQueryService**:
   - getPaginated(array  = [], int  = 15): Standard query with eager loading (rand, service, categories, solutions, seo).
   - indById(int ): Find product model with relations.
   - indBySlug(string ): Find product model with relations.
   - getBrands() / getCategories(): Retrieve active brands and categories.

2. **ProductMutationService**:
   - create(array ): Product: Creates product within a DB transaction, handles relationships (brand, service, categories, solutions), attaches SEO metadata, and runs image processing.
   - update(Product|int , array ): Product: Updates existing product attributes, updates or detaches relations, synchronizes SEO metadata.
   - delete(Product|int ): bool: Safely deletes product (triggering HasImageCleanup to purge orphan files).

3. **ProductImportService**:
   - importFromPayload(array ): Product: Encapsulates the specific import upsert semantics used by ProductApiController::import(), calling ProductMutationService.

### 2.3 Refactored REST Flow:
`	ext
POST /api/products/import
       │
       ▼
ProductApiController::import(Request , ProductImportService )
       │
       ▼
 = ->importFromPayload(->all());
       │
       ▼
return response()->json(['message' => '...', 'product' => new ProductResource()]);
`
*Result:* Existing REST API contract and behavior remain 100% identical.

### 2.4 MCP Tool Flow:
`	ext
MCP product.create / product.update / product.delete
       │
       ▼
ProductCreateTool / ProductUpdateTool / ProductDeleteTool
       │
       ▼
ProductMutationService / ProductQueryService
       │
       ▼
PostgreSQL
`

---

## 3. News Domain Service Boundaries

### 3.1 Current Responsibilities Breakdown
- **NewsController (Web):** Formats Inertia views for archive, category, and tag browsing.
- **DynamicResolverController (Web):** Resolves single news articles by slug with related sidebar posts.
- **NewsApiController (REST):**
  - index(): Queries published news, filters by category/tag, returns NewsResource::collection.
  - show(): Finds article by slug, returns 
ew NewsResource.
  - import(): 
    - Transaction management.
    - Model lookup (irstOrNew(['slug' => ])).
    - Attribute mapping & publishing timestamp.
    - Thumbnail download and WebP conversion via ImageHelper.
    - Category M2M syncing (NewsCategory::firstOrCreate + sync).
    - Tag M2M syncing (NewsTag::firstOrCreate + sync).
    - Polymorphic SEO metadata update ($news->seo()->updateOrCreate).

### 3.2 Target Service Architecture
We define two dedicated services under Modules/News/Services/:

1. **NewsQueryService**:
   - getPaginated(array  = [], int  = 15): Query published articles with categories, 	ags, uthor, seo.
   - indById(int ): Find article by ID.
   - indBySlug(string ): Find article by slug.
   - getCategories() / getTags(): Lookup categories and tags.

2. **NewsMutationService**:
   - create(array ): News: Creates article within a DB transaction, handles categories, tags, image processing, and SEO metadata.
   - update(News|int , array ): News: Updates article attributes and relations.
   - delete(News|int ): bool: Deletes article (triggers HasImageCleanup).
   - importFromPayload(array ): News: Preserves upsert import semantics for NewsApiController::import().

### 3.3 Refactored REST Flow:
`	ext
POST /api/news/import
       │
       ▼
NewsApiController::import(Request , NewsMutationService )
       │
       ▼
 = ->importFromPayload(->all());
       │
       ▼
return response()->json(['message' => '...', 'news' => new NewsResource()]);
`

---

## 4. Cross-Interface Compatibility Verification

| Domain Operation | Shared Service Method | REST Consumer | MCP Consumer |
| :--- | :--- | :--- | :--- |
| **Product List** | ProductQueryService::getPaginated | GET /api/products | product.list |
| **Product Detail** | ProductQueryService::findBySlug / indById | GET /api/products/{slug} | product.get |
| **Product Create** | ProductMutationService::create | (New capability) | product.create |
| **Product Update** | ProductMutationService::update | (New capability) | product.update |
| **Product Delete** | ProductMutationService::delete | (New capability) | product.delete |
| **Product Import** | ProductMutationService::importFromPayload | POST /api/products/import | product.import |
| **Brand List/Get** | ProductQueryService::getBrands | (Web Controller) | rand.list, rand.get |
| **Category List** | ProductQueryService::getCategories | (Web Controller) | product_category.list |
| **Article List** | NewsQueryService::getPaginated | GET /api/news | rticle.list |
| **Article Detail** | NewsQueryService::findBySlug / indById | GET /api/news/{slug} | rticle.get |
| **Article Create** | NewsMutationService::create | (New capability) | rticle.create |
| **Article Update** | NewsMutationService::update | (New capability) | rticle.update |
| **Article Delete** | NewsMutationService::delete | (New capability) | rticle.delete |
| **Article Import** | NewsMutationService::importFromPayload | POST /api/news/import | rticle.import |

---

## 5. Implementation Sequence & Safety Rules

1. **Step 1:** Extract ProductQueryService and ProductMutationService.
2. **Step 2:** Refactor ProductApiController to use the services. Run JsonApiResourceTest to verify zero regression.
3. **Step 3:** Extract NewsQueryService and NewsMutationService.
4. **Step 4:** Refactor NewsApiController to use the services. Run JsonApiResourceTest to verify zero regression.
5. **Step 5:** Scaffolding and testing MCP Core (pp/MCP/).
6. **Step 6:** Implement module MCP tools injecting these services.