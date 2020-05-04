# Yaraku Coding Task
 
Chris Liu (devbydesign.tech@gmail.com)
 
*04 May 2020*
 
## About The Coding Task
 
A simple CRUD web application that allows the user to Add, Edit, and Delete book titles and its author. Users can search and export a list of Titles or Authors or both as .CSV or XML.
 
### Technologies
Laravel backend with Vue frontend scaffolding. 
 
### devDependencies
- Vue-router
- SweetAlert2
- Vform
- SheetJS
- Axios
- mocha/mocha-webpack
- jsdom/jsdom-global
- Expect
 
### Testing 
The test uses Laravel’s built in PHPunit test and vue-utils. 
Run tests with:
```
php artisan test
npm run test
```
 
### Installation
Clone or download the repository.

`.env` settings:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yaraku_books
DB_USERNAME=root
DB_PASSWORD=
```

Seed the database for example data:
```
php artisan db:seed
```

Deploy on `localhost:8001`.
 
## Security
If you discover any security related issues, please email devbydesign.tech@gmail.com.
 
## License
The MIT License (MIT).
