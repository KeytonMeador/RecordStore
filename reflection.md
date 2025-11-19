1. In your own words, explain what require_login() does in index.php. When does it run, and what does it enforce?

- require_login() checks if the user has logged into their account. it runs whenever a user tries to do anything that require an account like adding an item to their cart or checking out. It enforces the user being logged in to perform acions. 

2. Describe the login process step-by-step: from clicking the “Login” button on the form to the moment the user is redirected. Which file and which case handles the logic? What session variables are set after a successful login?

- clicking login first checks if the username and password are valid, if they are not it gives the message "Invalid username or password", if either or both the fields are empty it gives the message "Enter both fields". It then redirects the user to the list view. The index file and the login case handle the logic. the set variables are user_id and full_name.

3. When you click “Add to Cart,” what exactly gets stored in $_SESSION['cart']? Which action adds items to the cart, and what type of data is being stored?

- The record_id is stored in $_SESSION['cart']. The action that adds items to the cart is pressing the add to cart button. the data that is stored is the record_id

4. On the cart page, you use $records_in_cart. Where does that variable come from, and why do we need records_by_ids() instead of just using the raw IDs in the session?

- records_in_cart comes from the cart view in index.php. we need records_by_ids because it keeps the formatting from the list view and it keeps all the nessecary information.

5. Explain what happens when you click “Complete Purchase.” Which action in index.php runs, what loop is executed, which function writes each record to the database, and which table is updated?

- The 'checkout' case in the switch is called, the (cart_ids as rid) foreach loop is executed. purchase_create writes each record to the database. The purchases table is updated.
