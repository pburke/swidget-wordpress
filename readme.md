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
  * Low Qty - The point when the *low quantity* message shows
  * Display Product Name - Show the name of the product (defaults to Yes)

##### Messages

  * Loading - The message that displays while the tickets are loading
  * **Not yet on sale** - Message displayed if the product is not yet available to purchase
  * **Offline sales only** - Message displayed when the item is available to be sold in Siriusware but *not* with e-commerce
  * **Expired** - The message that is displayed when the item is no longer on sale
  * **Low Qty** - The message that displays when there is low quantity
  * **Sold Out** - The message that displays when the item is sold out
  * Add To Cart - A message for when an item is added to cart (Note: only for swaddtocart widgets)

#####Text Modification

  * **Free** - The text to display when an item is free (Replaces $0.00).
  * **Additional Fee** - The text for additional fees
  * **Checkout Button** - The text for the checkout button. **Note:** There are separate entries for the quick widget and the cart widget
  * Add to Cart Button - Text for the cart widget's checkout button
  * **Discount** - The text for discounts
  * **Member Discount** - The text to show how much one would pay if they are a member


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
