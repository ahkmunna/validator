<?php

namespace ahkmunna\validator;

use Illuminate\Support\ServiceProvider;

class validatorServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'../lang/en/validation', 'validator');

        $this->publishes([
            __DIR__.'../lang/en/validation' => resource_path('lang/vendor/validator'),
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['validator']->extend(
            'composite_unique',
            function ($attribute, $value, $parameters, $validator)
            {
                // Clean the parameters
                $parameters = array_map( 'trim', $parameters );

                // Grab the table name and remove the it from parameters
                $table = array_shift( $parameters );

                // Check if the last parameter is a number and if so then assume the number is the exception ID
                $exceptionId = (bool) preg_match( '/^\d+$/', end($parameters) ) ? array_pop( $parameters ) : null ;

                // Now start building the conditional array.
                $wheres = $exceptionId ?
                        [
                            ['id', '!=', $exceptionId]
                        ] : [];

                // Add current key-value in conditional array
                $wheres[] = [ $attribute, '=', $value ];

                // iterates over the other given parameters and build the wheres array
                while ( $field = array_shift( $parameters ) )
                {
                    if ($field == $attribute) continue; //Check if the attribute passed in the parameter

                    $t = explode(':', $field); //extract parameters that have value

                    // Check if the parameter passed with value
                    if(isset($t[1])){
                        $t[1] = $t[1] != '' ? $t[1] : null; // IF the padded value is empty then assign NULL
                        $wheres[] = [ $t[0], '=', $t[1] ];  // Passed with value, so assign the value to the field
                    }
                    else{
                        $wheres[] = [ $field, '=', \Request::get( $field ) ];  // Passed field name only, so find the value in form request
                    }
                }

                // Our conditional array is ready and now query the table with all the conditions
                $result = \DB::table( $table )->where( $wheres )->first();

                //Return FLASE if any record found
                return empty( $result );
            }
        );
    }
}