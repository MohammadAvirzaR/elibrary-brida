# TODO: Fix Login Double Hashing Issue

## Steps to Complete
- [ ] Edit AuthController.php to remove manual bcrypt in register method, letting the User model mutator handle password hashing.
- [ ] Test login functionality to ensure it works after the fix.
