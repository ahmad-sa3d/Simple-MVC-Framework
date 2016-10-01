## PHP-Simple MVC Framework


> This is simple MVC PHP Framework May Be Suitable For Small And Medium projects According To Your Project Requirements, It Is **`REST API`** , **`Clean Uris`**, **`MVC`** Pattern, __`View Templating`__ And __`Smart Response`__ , And Othere Features You Will Explore
> 
> I'm a big Fan of `Laravel` Framework And Very Thanks To `Taylor Otwell` and Other Contributors For His Great Framework, So You Might Find here Some method names like in Laravel

### License

>This Work is licensed under a Creative Commons Attribution-ShareAlike 4.0 International License.
>
[![Link](https://i.creativecommons.org/l/by-sa/4.0/88x31.png)](http://creativecommons.org/licenses/by-sa/4.0/)

### Copyright
>@author	__Ahmed Saad__ <a7mad.sa3d.2014@gmail.com> 2012-2016

### Version
> 2.0.0

### Requirements
> Framework uses Clean URIs so it needs `mod_rewrite` apache module to be enabled


----

### Features:

1. MVC (Model, View And Controller) Pattern
2. REST API for `CRUD` Operations
3. Clean `URI`
4. Gateway To Protect Uris
5. Handle Validation, With Custom Validation Error Messages Very Useful If You need Error messages In Another Representation Or Language
6. Smart Response, Useful For APIs and Json Response
7. Handle Uploads And Inputs
8. Controller Record Model Injection For Known Methods
	Like show, edit, delete, destroy, update, store
9. SimpleDateTime Class For Dealing With dates
10. Model Auto Casting, By Default `created_at` and `updated_at` Timestamps Attributes will be Auto casted For Any Model If you want to override this behaviour By adding fields or remove fields you can define protected property named `timestamps` ass array and type date fields that you want to be casted , And For Other fields Casting types You can Define Your casting from one of ( integer, boolean, array ) by adding protected array property called `casts` and add `[field => cast_type]`
11. View Basic Templating Like `@extends`, `@include`, `@section @stop`, `@yield`, `{{  }}`, `{!!  !!}`
*Template Loops `@for` `@foreach` and Conditions `@if` Currently Not Supported You Can Use Native*
13. Access to Old Inputs, Notiications, Validation Errors From View
14. `Well Documented Library`
15. And Many Other Features You Can Explore.
16. Include Admin Area With Full User Management
	To Demonestrate How It Works

---
### Usage:

> __Directory Structure:__
> 
> >`App`
> >>`Config` *Application Configuration*
> >
> >>`Controllers` *Application Controllers*
> >>>`Admin` *Admin Area Controllers*
> >>
> >>`Library` *Framework Core Library*
> >>
> >>`Model` *Application Models*
> >>
> >>`View` *Application Views*
> >
> >`assets` *Application Public Accisible Area*
>
> -
> __Configuration:__ App\Config\
> > `database.php` is to configure your database.
> 
> > `gateway.php` 	is to configure Application Access For Specific Uris.
> 
>> `simpleDateTimeLocals.php`  to Specify Locals For SimpleDateTime Class.
>
> -
> __Application Routes:__
> 
> >No Need To Define Routes Framework Will Automatically Analyze Request URI And Accordingly Will Try To Load Controller And Method If Found Ex:
>
>>>`my-app.dev/`
>
>>>`GET` Request will Call `IndexController::index()`
>>> Default Controller is IndexController
>
>>`my-app.dev/test`
>
>>>`GET` Request will Call `TestController::index()`
>>>
>>>`POST` Request Will Call `TestController::store( Request $request )`
>
>>`my-app.dev/test/1`
>
>>>
>>*Second URI Part Is __Numeric__*
>>
>>>`GET` Request will Call `TestController::show( Test $record_instance )` Note that `Test` Model Instance Will Be Injected Automatically, Model Name Is The Same AS Controller Name without _`Controller`_ Keyword
>>>
>>>`PUT` OR `PATCH` Request Will Call `TestController::update( Test $record, Request $request )`
>>>
>>>`DELETE` Request Will Call `TestController::destroy( Test $record, Request $request )`
>
>>-
>>*Second URI Part Is __String__*
>
>> Edit And delete Uris, Will Inject Model Instance Automatically
>>>`my-app.dev/test/edit/1`
>
>>>`GET` Request will Call `TestController::edit( Test $record_instance )`
>
>>`my-app.dev/test/delete/1`
>
>>>`GET` Request will Call `TestController::delete( Test $record_instance )`
>
>> Other URIs
>> 
>>`my-app.dev/test/method_name/parameter1/parameter2/..`
>>>`GET` Request will Call `TestController::methodName( $parameter1, $parameter2[, ..] )`
>>
>>> Other Request Methods ARE `CUD` So They Requires $parameter1 To Be ID of Controller Corresponding Model AND Other Parameters Are Ignored
>>
>>>`post` will Call `TestController::postMethodName( Test $record, Request $request )`
>>>
>>>`PUT` OR `'PATCH` will Call `TestController::updateMethodName( Test $record, Request $request )`
>>>
>>>`DELETE` will Call `TestController::deleteMethodName( Test $record, Request $request )`
>
> -
>__For Admin Area:__
>
>> `my-app.dev/admin`
>> 
>> `/admin` uri will be used to access admin area
>> 
>> controllers will be loaded From `admin` directory inside `controllers` directory
>> 
>> default admin controller is `DashboardController` is for `/admin` uri
>
>--
>__Note:__
>
> When Framework It Automatically Inject Model For Controller Method It Will search for Model name That Have The same Name As Controller, So While Naming Controllers And Models Try To Match Both if in `Singular` Or `Plural` as you wish

---
###How It Works:

there are Simple admin area included with framework ( You Can Improve Access Roles By Creating Your Database Roles And Permissions Table Structurs And Connect Them With Users, Then Add Checks In Gateway Configuration File ), with users management you can explore `UserController` and `User` Model and `user views` to see How It Works
> create your database
> 
> Set Database Configuration
> 
>Import included Database ( _it is users table structure and data_ )