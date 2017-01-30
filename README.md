# Composite Unique Validator
A muti-column unique validation extension with exception for Laravel 5.*

## Installation
Install the package through [Composer](http://getcomposer.org/).

Run the Composer require command from the Terminal:

    composer require ahkmunna/validator

Now all you have to do is add the service provider of the package and alias the package. To do this open your `config/app.php` file.

Add a new line to the `providers` array:

    ahkmunna\validator\validatorServiceProvider::class

Now you're ready to start using the validator in your application.

## Usage

###case 1
Like any laravel validation rules:

    $rules = array(
        'field_name' => 'composite_unique:table, unique_column_1, unique_column_2, unique_column_3',
    );


###case 2
Pass a value or Check uniqueness with a field that not in current form request:

    $rules = array(
        'field_name' => 'composite_unique:table, unique_column_1, unique_column_2:4, unique_column_3',
    );

unique_column_2 is passed with a value so the validator will ignore the form request value and compare the field with the given value, It is useful sometimes, you can give a field name that not exist in the form request but in the database table.

###case 3
Check uniqueness with exception:

    $rules = array(
        'field_name' => 'composite_unique:table, unique_column_1, unique_column_2, unique_column_3, 1',
    );

The last parameter is a primary key of the table that row will be ignored. Useful for update operation
