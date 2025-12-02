# 🎨 UML DIAGRAMS - MERMAID FORMAT

# Buka di: https://mermaid.live atau langsung di GitHub

## 1️⃣ CLASS DIAGRAM - MERMAID

```mermaid
classDiagram
    class CashierController {
        -menus: Menu[]
        -categories: Category[]
        -cart: array
        -order: Order
        +index() View
        +addToCart(Request) JsonResponse
        +updateCart(Request, menuId) JsonResponse
        +removeFromCart(menuId) JsonResponse
        +getCart() JsonResponse
        +checkout(Request) JsonResponse
        +clearCart() JsonResponse
        +printReceipt(orderId) View
    }

    class Menu {
        -id: int
        -name: string
        -price: decimal
        -stock: int
        -sold_quantity: int
        -image_url: string
        +hasEnoughStock(quantity) bool
        +decreaseStock(quantity) void
        +increaseStock(quantity) void
    }

    class Order {
        -id: int
        -order_number: string
        -total_amount: decimal
        -total_quantity: int
        -payment_method: enum
        -status: enum
        -completed_at: timestamp
        -created_at: timestamp
        -updated_at: timestamp
    }

    class OrderItem {
        -id: int
        -order_id: int
        -menu_id: int
        -quantity: int
        -price: decimal
        -discount: decimal
        -created_at: timestamp
    }

    class Category {
        -id: int
        -name: string
        -created_at: timestamp
        -updated_at: timestamp
    }

    CashierController --> Menu: uses
    CashierController --> Order: creates
    CashierController --> OrderItem: creates
    Menu --> Category: belongsTo
    Order --> OrderItem: hasMany
    OrderItem --> Menu: belongsTo
    OrderItem --> Order: belongsTo
```

---

## 2️⃣ SEQUENCE DIAGRAM - addToCart() FLOW

```mermaid
sequenceDiagram
    actor User as Cashier User
    participant Browser
    participant Controller as CashierController
    participant Menu as Menu Model
    participant Session as Session Storage

    User->>Browser: Click Add to Cart
    Browser->>Controller: POST /cashier/add-to-cart
    Note over Controller: Validate Request

    Controller->>Menu: Find Menu by ID
    Menu-->>Controller: Menu Object

    Controller->>Controller: Check Stock with hasEnoughStock()

    alt Stock Not Sufficient
        Controller-->>Browser: Error 422 JSON
        Browser-->>User: Show: "Stok tidak cukup"
    else Stock Sufficient
        Controller->>Session: Get current cart
        Session-->>Controller: Cart Array

        alt Item Already in Cart
            Controller->>Controller: Calculate new total qty
            Controller->>Controller: Validate new qty vs stock

            alt New Qty > Stock
                Controller-->>Browser: Error 422 JSON
            else OK
                Controller->>Session: Update item quantity
            end
        else First Time Add
            Controller->>Session: Add new item to cart
        end

        Session-->>Controller: Cart saved
        Controller-->>Browser: Success JSON + Cart Data
        Browser-->>User: Show: "Added to Cart"
    end
```

---

## 3️⃣ SEQUENCE DIAGRAM - checkout() FLOW

```mermaid
sequenceDiagram
    actor Cashier
    participant Browser
    participant Controller as CashierController
    participant Menu as Menu Model
    participant Order as Order Model
    participant OrderItem as OrderItem Model
    participant Session
    participant Database as MySQL DB

    Cashier->>Browser: Click Checkout
    Browser->>Controller: POST /cashier/checkout

    Note over Controller: Validate payment_method & discount

    Controller->>Session: Get cart items
    Session-->>Controller: Cart data

    alt Cart is Empty
        Controller-->>Browser: Error 400 JSON
    else Cart has Items
        loop For Each Item in Cart
            Controller->>Menu: Check hasEnoughStock()

            alt Stock Insufficient
                Controller-->>Browser: Error 422 JSON
                Note over Browser: Show error & stop
                break
            end
        end

        Note over Controller: All items OK - Calculate totals
        Controller->>Controller: Calculate subtotal, discount, final amount

        rect rgb(200, 220, 255)
            Note over Controller,OrderItem: Transaction Start

            Controller->>Order: Create Order Record
            Database->>Order: INSERT Order
            Order-->>Controller: Order ID returned

            loop For Each Cart Item
                Controller->>OrderItem: Create OrderItem
                Database->>OrderItem: INSERT OrderItem

                Controller->>Menu: Increment sold_quantity
                Database->>Menu: UPDATE sold_quantity++

                Controller->>Menu: Decrease stock
                Database->>Menu: UPDATE stock--
            end

            rect rgb(200, 255, 200)
                Note over Controller,Session: Clear Session
                Controller->>Session: forget('cashier_cart')
            end
        end

        Controller-->>Browser: Success JSON + Order Data
        Browser-->>Cashier: Show Receipt
    end
```

---

## 4️⃣ STATE DIAGRAM - CART & ORDER STATES

```mermaid
stateDiagram-v2
    [*] --> EmptyCart

    EmptyCart --> CartWithItems: addToCart(menu_id, qty)

    CartWithItems --> CartWithItems: addToCart()\n(same item)\nOR\nupdateCart()

    CartWithItems --> CartWithItems: removeFromCart()\n(if >1 item)

    CartWithItems --> EmptyCart: removeFromCart()\n(last item)\nOR\nclearCart()

    CartWithItems --> OrderCreated: checkout()\n✓ Stock OK

    CartWithItems --> CartWithItems: Stock validation failed\n❌ Error 422

    OrderCreated --> [*]

    EmptyCart --> [*]
```

---

## 5️⃣ ACTIVITY DIAGRAM - COMPLETE CHECKOUT

```mermaid
stateDiagram-v2
    [*] --> ValidateInput

    ValidateInput --> ValidateCart: Input OK
    ValidateInput --> Error1: Invalid input

    Error1 --> [*]

    ValidateCart --> StockCheck: Cart not empty
    ValidateCart --> Error2: Cart empty

    Error2 --> [*]

    StockCheck --> StockCheckLoop: Start checking each item

    StockCheckLoop --> StockOK: All items have stock
    StockCheckLoop --> Error3: Any item insufficient

    Error3 --> [*]

    StockOK --> CalculateTotals: All validations pass

    CalculateTotals --> CreateOrder: Subtotal calculated

    CreateOrder --> CreateOrderItems: Order created

    CreateOrderItems --> UpdateStock: All items stored

    UpdateStock --> ClearCart: Stock decreased

    ClearCart --> Success: Transaction complete

    Success --> [*]
```

---

## 6️⃣ COMPONENT DIAGRAM - LAYERED ARCHITECTURE

```mermaid
graph TB
    subgraph UI["🎨 PRESENTATION LAYER"]
        CashierView["Cashier Interface View"]
        CartDisplay["Cart Display Component"]
        ReceiptTemplate["Receipt Template"]
    end

    subgraph Logic["⚙️ APPLICATION LOGIC LAYER"]
        CashierCtrl["CashierController"]
    end

    subgraph Business["💼 BUSINESS LOGIC LAYER"]
        MenuModel["Menu Model<br/>+ hasEnoughStock()<br/>+ decreaseStock()"]
        OrderModel["Order Model"]
        OrderItemModel["OrderItem Model"]
    end

    subgraph Data["🗄️ DATA ACCESS LAYER"]
        EloquentORM["Eloquent ORM<br/>Query Builder<br/>Model Relations"]
    end

    subgraph DB["💾 DATABASE LAYER"]
        MySQL["MySQL Database<br/>Tables: menus, orders,<br/>order_items, categories"]
    end

    CashierView -->|HTTP Request| CashierCtrl
    CashierCtrl -->|JSON Response| CashierView
    CartDisplay -->|Display| CashierView
    ReceiptTemplate -->|Display| CashierView

    CashierCtrl -->|Business Logic| MenuModel
    CashierCtrl -->|Create| OrderModel
    CashierCtrl -->|Create| OrderItemModel

    MenuModel -->|ORM| EloquentORM
    OrderModel -->|ORM| EloquentORM
    OrderItemModel -->|ORM| EloquentORM

    EloquentORM -->|SQL Queries| MySQL

    style UI fill:#e1f5ff
    style Logic fill:#fff3e0
    style Business fill:#f3e5f5
    style Data fill:#e8f5e9
    style DB fill:#fce4ec
```

---

## 7️⃣ USE CASE DIAGRAM

```mermaid
graph LR
    Cashier["👤 Cashier"]

    subgraph ViewPhase["1️⃣ View Phase"]
        ViewMenu["View Available<br/>Menus"]
        FilterCat["Filter by<br/>Category"]
        ViewDetails["View Menu<br/>Details"]
    end

    subgraph CartPhase["2️⃣ Cart Management"]
        AddCart["Add to Cart"]
        UpdateQty["Update<br/>Quantity"]
        RemoveCart["Remove from<br/>Cart"]
        ViewCart["View Cart<br/>Total"]
    end

    subgraph CheckoutPhase["3️⃣ Checkout"]
        SelectPayment["Select Payment<br/>Method"]
        EnterDiscount["Enter Discount<br/>Percentage"]
        FinalValidate["Final Stock<br/>Validation"]
    end

    subgraph ProcessPhase["4️⃣ Process & Complete"]
        CreateOrder["Create Order<br/>in Database"]
        DecreaseStock["Decrease Menu<br/>Stock"]
        PrintReceipt["Print Receipt"]
        ClearCart["Clear Cart"]
    end

    Cashier --> ViewMenu
    ViewMenu --> FilterCat
    FilterCat --> ViewDetails
    ViewDetails --> AddCart

    AddCart --> UpdateQty
    UpdateQty --> RemoveCart
    RemoveCart --> ViewCart
    ViewCart --> SelectPayment

    SelectPayment --> EnterDiscount
    EnterDiscount --> FinalValidate

    FinalValidate -->|✓ All OK| CreateOrder
    FinalValidate -->|❌ Stock Failed| AddCart

    CreateOrder --> DecreaseStock
    DecreaseStock --> ClearCart
    ClearCart --> PrintReceipt
    PrintReceipt --> Cashier

    style ViewPhase fill:#e3f2fd
    style CartPhase fill:#f3e5f5
    style CheckoutPhase fill:#fce4ec
    style ProcessPhase fill:#e8f5e9
```

---

## 8️⃣ DATA FLOW - SIMPLIFIED

```mermaid
graph LR
    User["👤 Cashier User"]
    Browser["🌐 Browser"]
    Server["🖥️ CashierController"]
    Menu["📦 Menu Model"]
    Order["📋 Order Model"]
    DB["💾 MySQL Database"]
    Session["📝 Session Storage<br/>cashier_cart"]

    User -->|Click UI| Browser
    Browser -->|HTTP Request| Server

    Server -->|Query| Menu
    Menu -->|Data| Server

    Server -->|Check Stock| Menu
    Menu -->|OK/FAIL| Server

    Server <-->|Store/Get| Session

    Server -->|CREATE| Order
    Order -->|Persist| DB

    Server -->|UPDATE| Menu
    Menu -->|Persist| DB

    Server -->|Response| Browser
    Browser -->|Display| User

    style User fill:#fff9c4
    style Browser fill:#bbdefb
    style Server fill:#c8e6c9
    style Menu fill:#b2dfdb
    style Order fill:#f8bbd0
    style DB fill:#ffe0b2
    style Session fill:#d1c4e9
```

---

## 9️⃣ CONTROL FLOW - addToCart()

```mermaid
flowchart TD
    A["START<br/>addToCart"] --> B["Validate<br/>Input"]

    B --> C{menu_id &<br/>quantity<br/>valid?}
    C -->|NO| D["Return Error<br/>422"]
    C -->|YES| E["Get Menu<br/>from DB"]

    E --> F{Menu<br/>exists?}
    F -->|NO| G["Throw 404"]
    F -->|YES| H["Check Stock:<br/>hasEnoughStock<br/>qty?"]

    H --> I{Stock<br/>sufficient?}
    I -->|NO| J["Return Error 422<br/>Stok tidak cukup"]
    I -->|YES| K["Get Cart<br/>from Session"]

    K --> L{Item in<br/>cart?}
    L -->|YES| M["Calculate:<br/>newQty"]
    L -->|NO| N["Create new<br/>item entry"]

    M --> O{Check new<br/>qty vs<br/>stock?}
    O -->|NO| P["Return Error 422<br/>Qty melebihi stok"]
    O -->|YES| Q["Update item<br/>qty in cart"]

    N --> R["Add item<br/>to cart"]
    Q --> S["Save cart<br/>to Session"]
    R --> S

    S --> T["Return Success<br/>JSON"]
    T --> U["END"]

    D --> V["Error Response"]
    G --> V
    J --> V
    P --> V
    V --> U

    style A fill:#e3f2fd
    style T fill:#c8e6c9
    style D fill:#ffcdd2
    style G fill:#ffcdd2
    style J fill:#ffcdd2
    style P fill:#ffcdd2
```

---

## 🔟 CONTROL FLOW - checkout()

```mermaid
flowchart TD
    A["START<br/>checkout"] --> B["Validate Request:<br/>payment_method<br/>discount"]

    B --> C{Valid?}
    C -->|NO| D["Return Error 422"]

    C -->|YES| E["Get Cart<br/>from Session"]

    E --> F{Cart<br/>empty?}
    F -->|YES| G["Return Error 400<br/>Keranjang kosong"]
    F -->|NO| H["BEGIN<br/>TRY BLOCK"]

    H --> I["FOR EACH<br/>cart item"]

    I --> J["Get Menu<br/>from DB"]

    J --> K{Stock<br/>sufficient?}
    K -->|NO| L["Return Error 422<br/>Stok kurang"]
    K -->|YES| M{More<br/>items?}

    M -->|YES| I
    M -->|NO| N["All items<br/>validated ✓"]

    N --> O["Calculate<br/>Subtotal"]

    O --> P["Apply<br/>Discount"]

    P --> Q["Calculate<br/>Final Amount"]

    Q --> R["CREATE Order<br/>in DB"]

    R --> S["FOR EACH<br/>item"]

    S --> T["CREATE<br/>OrderItem"]

    T --> U["Update<br/>sold_quantity++"]

    U --> V["Decrease<br/>stock--"]

    V --> W{More<br/>items?}

    W -->|YES| S
    W -->|NO| X["Clear Cart<br/>from Session"]

    X --> Y["Return<br/>Success JSON"]

    Y --> Z["END"]

    D --> AA["ERROR<br/>Response"]
    G --> AA
    L --> AA
    AA --> Z

    style A fill:#e3f2fd
    style Y fill:#c8e6c9
    style D fill:#ffcdd2
    style G fill:#ffcdd2
    style L fill:#ffcdd2
```

---

Generated: 2 Desember 2025
Format: Mermaid Diagram
Open at: https://mermaid.live
