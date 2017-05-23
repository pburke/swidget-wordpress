# Swidget Wordpress Plugin

This is a wordpress plugin to display swidgets.  It simplifies the process for adding the swidget to the page by utilizing shortcodes.  There are 3 shortcodes that this plugin adds, see below for instructions.

## Install

To install download this project and copy the "swidget" folder into your wordpress plugin directory located at /wp-content/plugins

## Shortcodes

### Quick Checkout

```
[swcheckout site="siteID" item="itemID"]
```

### Cart

```
[swcart site="siteID"]
```

### Add to Cart

```
[swaddtocart site="siteID" item="itemID"]
```
### Options

#### Option Reference
  * Date Format - How dates are displayed in placeholders.  Uses the moment.js library.  [Info on formats found here](http://momentjs.com/docs/#/displaying/format/)
  * Display Product Name - Show the name of the product (defaults to true)
  * Low Qty - The point when the *low quantity* message shows
  * **Message - Expired** - The message that is displayed when the item is no longer on sale
  * Message - Loading - The message that displays while the tickets are loading
  * **Message - Low Qty** - The message that displays when there is low quantity
  * **Message - Sold Out** - The message that displays when the item is sold out
  * Message - Add To Cart - A message for when an item is added to cart (Note: only for swaddtocart widgets)
  * **Text - Additional Fee** - The text for additional fees
  * **Text - Checkout Button** - The text for the checkout button (2 settings, for quick checkout and cart checkout)
  * Text - Cart Checkout Button - Text for the cart widget's checkout button
  * **Text - Discount** - The text for discounts
  * **Text - Member Discount** - The text to show how much one would pay if they are a member
  * **Text - Free** - The text to display when an item is free.

#### Placeholders

Place holders are a special string which will be replace with information from the item

**Bold** options above can use placeholders

 * #{stock} - How many tickets are remaining
 * #{name} - The name of the ticket
 * #{start_sale} - When the tickets go on sale
 * #{end_sale} - When the tickets go off sale (both online and offline)

#### Options Hierarchy

  The following is the priority of where the widget gets it's settings from (lower numbers trump higher numbers)

 2. Options set in WP admin
 3. Default settings from the widget itself.

#### Message Hierarchy

 The following is the priority of the messages

 1. Past sale end
 2. Sold Out
 3. Offline sales only
 4. Prior to sale start
 5. [No message, ticket can be sold]
