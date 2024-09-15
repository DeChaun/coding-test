# Discount calculator API

## Assumptions

Some assumptions and uncertainties on the implementation are listed here. They should be clarified.
 - HighTotalRevenue discounts are available for users who have a revenue above € 1000. Assumed the € 1000 points to the revenue on previous sales, so without taking the current order's total into account.
 - CheapestProductPercentage uses the cheapest product (unit price retrieved from the incoming API request), not considering the quantity. The total price for an order item (unit price * quantity) will therefore not necessarily be the lowest of the order.
 - The order of the discounts is not yet determined. Assumed the order in the test description is uncommon and therefore possibly incorrect.
 - Due to floating-point issues (e.g. rounding to 88.80000000000001 instead of 88.8) in PHP, some values may have been rounded to 4 decimals to be more accurate.
