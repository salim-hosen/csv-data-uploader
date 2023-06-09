### 1. Downloade or Clone Project
Run this at the command line:
```
git clone https://github.com/salim-hosen/csv-data-uploader.git
```
### 2. Install Laravel
- For laravel packages and dependencies.
```
composer install
```
- Copy `.env.example` to `.env` and change app url and database info.

- `php artisan key:generate` for generate app key.

- Update `.env` file `QUEUE_CONNECTION=database`

- For generate database `php artisan migrate `

### 3. Install JavaScript Packages
- For JavaScript packages and dependencies.
```
npm install
```
- To start js package compiling run `npm run dev` 

### 4.Run The Project

- To run in browser `php artisan serve`

- Run `php artisan queue:work` to start the queue (important)

- Then Browse `http://127.0.0.1:8000`


### 5. Technologies Used

- PHP Laravel Framework
- ReactJS
- MySQL

### 6. Example Used CSV File
My sample excel file is dummy.csv file in CSV folder
`CSV/dummy.csv `

### License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
