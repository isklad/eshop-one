# TWidgetChange

`TWidgetChange` is the payload emitted by the widget whenever relevant state changes (delivery, payment, checkout extras, order, etc.). Defined in `src/domain/Widget/types/TWidget.ts`.

```ts
interface TWidgetChange {
  deliveryCountry?: TCountry
  deliveryOption?: TDeliveryOption
  deliveryPoint?: TDeliveryPoint
  packetaPoint?: TPacketaPoint
  deliveryAddress?: TDeliveryAddress
  paymentMethod?: TPaymentMethod
  checkoutExtras?: TCheckoutExtras[]
  mainAddress?: TDeliveryAddress
  shoppingCartPrice?: number
  currency: TCurrency
  order?: TOrder
}
```

## Properties

| Property | Type | Optional | Description |
|---|---|---|---|
| `deliveryCountry` | `TCountry` | Yes | The currently selected delivery country. |
| `deliveryOption` | `TDeliveryOption` | Yes | The currently selected delivery option (carrier/method). |
| `deliveryPoint` | `TDeliveryPoint` | Yes | The selected pickup point, present only when `deliveryOption.deliveryMode === 'point'`. |
| `packetaPoint` | `TPacketaPoint` | Yes | The selected Packeta pickup point, present only when `deliveryOption.deliveryMode === 'packeta'`. |
| `deliveryAddress` | `TDeliveryAddress` | Yes | The selected delivery address, present only when `deliveryOption.deliveryMode === 'address'`. |
| `paymentMethod` | `TPaymentMethod` | Yes | The selected payment method, present only during the `checkout` flow. |
| `checkoutExtras` | `TCheckoutExtras[]` | Yes | Selected checkout extras (add-ons), present only during the `checkout` flow. |
| `mainAddress` | `TDeliveryAddress` | Yes | The customer's main/billing address, present only during the `checkout` flow. |
| `shoppingCartPrice` | `number` | Yes | Total shopping cart price, present only during the `checkout` flow. |
| `currency` | `TCurrency` | No | The active currency. |
| `order` | `TOrder` | Yes | The created order, present once an order has been placed. |

## Related types

### `TCountry`
`src/domain/Country/types/TCountry.ts`

```ts
interface TCountry {
  id: number
  shopCountrySetting: number | null
  name: string
  code: string
  intlLocale: string
}
```

### `TDeliveryOption`
`src/domain/DeliveryOption/types/TDeliveryOption.ts`

```ts
interface TDeliveryOption {
  id: number
  deliveryType: 'b2c' | 'c2c' | 'c2b'
  transportType: 'cargo' | 'pickup' | 'plane' | 'truck'
  image: string
  name: string
  deliveryMode: 'point' | 'address' | 'packeta'
  packetaCarrier: string | null
  price: number
  deliveryDate: string // ISO datetime UTC
  addressLookupService: 'google' | 'egon'
}
```

### `TDeliveryPoint`
`src/domain/DeliveryPoint/types/TDeliveryPoint.ts`

```ts
interface TDeliveryPoint {
  name: string
  gps: { lat: number, lon: number }
  id: number
  country: number
  externalId: string | null
  image: string
  address: TAddress
  inOperation: boolean
  workingHours: {
    monday: { from: string, to: string }[]
    tuesday: { from: string, to: string }[]
    wednesday: { from: string, to: string }[]
    thursday: { from: string, to: string }[]
    friday: { from: string, to: string }[]
    saturday: { from: string, to: string }[]
    sunday: { from: string, to: string }[]
  } | null
  capabilities: {
    codAllowed: boolean | null
    creditCardAllowed: boolean | null
    cashAllowed: boolean | null
    dropOffAllowed: boolean | null
  }
  proximity: number
}
```

### `TPacketaPoint`
`src/domain/Packeta/types/TPacketa.ts`

```ts
type TPacketaPoint = {
  pickupPointType: 'external' | 'internal'
  externalId: string                 // unique point ID
  id: string | null                  // packeta branch point ID (when pickupPointType = 'internal')
  carrierPickupPointId: string | null // external carrier point ID (when pickupPointType = 'external')
  carrierId: string | null           // external carrier ID (packeta version of delivery-option)
  place: string                      // name of the point
  street: string
  city: string
  zip: string
  country: string
  currency: string
  creditCardPayment: boolean
  gps: { lat: number, lon: number }
}
```

### `TDeliveryAddress`
`src/domain/DeliveryAddress/types/TDeliveryAddress.ts`

```ts
interface TDeliveryAddress {
  id: string | null
  country: number
  device: string
  person: TPerson
  address: TAddress
  main: boolean
}
```

### `TAddress`
`src/domain/App/types/TAddress.ts`

```ts
interface TAddress {
  name: string
  company: string
  businessId: string
  taxId: string
  vatId: string
  street: string
  streetNumber: string
  city: string
  postalCode: string
}
```

### `TPerson`
`src/domain/Person/Types/TPerson.ts`

```ts
interface TPerson {
  name: string
  surname: string
  email: string
  phone: string
  phoneRegion: string // ISO country code, e.g. 'CZ', 'US'
}
```

### `TPaymentMethod`
`src/domain/PaymentMethod/TPaymentMethod.ts`

```ts
interface TPaymentMethod {
  id: number
  name: string
  externalId: string
  price: number
  imageUrl: string
  variant: 'onlineCardPayment' | 'cashOnDelivery' | 'onlineTransfer'
  nameTransKey: string
}
```

### `TCheckoutExtras`
`src/domain/CheckoutExtras/types/TCheckoutExtras.ts`

```ts
interface TCheckoutExtras {
  id: number
  name: string
  price: number
  icon: string
  nameTransKey: string
}
```

### `TOrder`
`src/domain/Order/types/TOrder.ts`

```ts
interface TOrder {
  id: string
}
```

### `TCurrency`
`src/domain/Currency/types/TCurrency.ts`

```ts
interface TCurrency {
  id: number
  code: string
  rateEur: number
  roundingPrecision: number | null
}
```
