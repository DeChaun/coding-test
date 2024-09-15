# Discount calculator API

## Setup

To set the project up, some commands are defined in the Makefile. Please make sure docker is installed on your machine and you're starting from the current folder. Then follow these steps:

1. Execute setup script
```shell
make setup
```

2. Start the PHP docker container
```shell
make start
```

The projects should now be up and running. It is available at http://coding-test.localhost:9000.

## Requests

### Request a discount price

```
    Method: POST
    Endpoint: /order/calculate-discount
```

Example request body:
```json
    {
      "id": "1",
      "customer-id": "2",
      "items": [
        {
          "product-id": "A102",
          "quantity": "2",
          "unit-price": "4.99",
          "total": "49.90"
        },
        {
          "product-id": "A101",
          "quantity": "20",
          "unit-price": "3",
          "total": "60"
        }
      ],
      "total": "49.90"
    }
```

Example response:
```json
{
    "id": 1,
    "customer-id": 2,
    "items": [
        {
            "product": "A102",
            "quantity": 2,
            "unitPrice": 4.99,
            "totalPrice": 49.9
        },
        {
            "product": "A101",
            "quantity": 20,
            "unitPrice": 3,
            "totalPrice": 60
        }
    ],
    "total": 49.9,
    "discount": [
        {
            "type": "CheapestProductPercentageDiscount",
            "explanation": "If you buy 2 or more products of category id 1, you get a 20% discount on the cheapest product. This results in a € 12 discount.",
            "discount-amount": 12
        },
        {
            "type": "HighTotalRevenueDiscount",
            "explanation": "A customer who has already bought for over € 1000, gets a discount of 10% on the whole order. This results in € 3.411 discount",
            "discount-amount": 3.411
        }
    ],
    "discountedTotal": 34.11
}
```

There is also a [postman collection](./docs/postman_collection.json) available in the docs folder.

## Assumptions

Some assumptions and uncertainties on the implementation are listed here. They should be clarified.
 - HighTotalRevenue discounts are available for users who have a revenue above € 1000. Assumed the € 1000 points to the revenue on previous sales, so without taking the current order's total into account.
 - CheapestProductPercentage uses the cheapest product (unit price retrieved from the incoming API request), not considering the quantity. The total price for an order item (unit price * quantity) will therefore not necessarily be the lowest of the order.
 - The order of the discounts is not yet determined. Assumed the order in the test description is uncommon and therefore possibly incorrect.
 - Due to floating-point issues (e.g. rounding to 88.80000000000001 instead of 88.8) in PHP, some values may have been rounded to 4 decimals to be more accurate.
