<div style="display:flex; align-items: center">
  <h1 style="position:relative; top: -6px" >Epic movie quotes</h1>
</div

---
In epic movie quotes you can go through authorization with specific email or quickly authorize with google account. After authorization you are redirected to news feed page where you can see newest posts. You can update your credentials by going to profile page. You can also add movies and add quotes as posts which you can delete or modify however you want. You can also like and comment someone elses post and they will get your interaction to posts via notifications. This website has English and Georgian languages support.

#
### Table of Contents
* [Prerequisites](#prerequisites)
* [Tech Stack](#tech-stack)
* [Getting Started](#getting-started)
* [Migrations](#migration)
* [Development](#development)
* [Project Structure](#project-structure)

#
### Prerequisites

* <img src="readme/assets/php.svg" width="35" style="position: relative; top: 4px" /> *PHP@7.2 and up*
* <img src="readme/assets/mysql.png" width="35" style="position: relative; top: 4px" /> *MYSQL@8 and up*
* <img src="readme/assets/npm.png" width="35" style="position: relative; top: 4px" /> *npm@6 and up*
* <img src="readme/assets/composer.png" width="35" style="position: relative; top: 6px" /> *composer@2 and up*


#
### Tech Stack

* <img src="readme/assets/laravel.png" height="18" style="position: relative; top: 4px" /> [Laravel@10.11.0](https://laravel.com/) - back-end framework
* <img src="readme/assets/spatie.png" height="19" style="position: relative; top: 4px" /> [Spatie Translatable](https://github.com/spatie/laravel-translatable) - package for translation
* <img src="readme/assets/laravel.png" height="18" style="position: relative; top: 4px" /> [Laravel sanctum@3.2](https://laravel.com/docs/10.x/sanctum) - Laravel featherweight authentication system
* <img src="readme/assets/laravel.png" height="18" style="position: relative; top: 4px" /> [Laravel socialite@5.6](https://laravel.com/docs/10.x/socialite) - Provides a way to authenticate with OAuth providers
* <img src="readme/assets/pusher.png" height="18" style="position: relative; top: 4px" /> [Pusher@7.2](https://laravel.com/docs/10.x/broadcasting) - used to implement realtime, live-updating user interfaces

#
### Getting Started
1\. First of all you need to clone E Space repository from github:
```sh
git clone https://github.com/RedberryInternship/jarji-abulashvili-epic-movie-quotes-back.git
```

2\. Next step requires you to run *composer install* in order to install all the dependencies.
```sh
composer install
```

3\. after you have installed all the PHP dependencies, it's time to install all the JS dependencies:
```sh
npm install
```

and also:
```sh
npm run dev
```
in order to build your JS/SaaS resources.

4\. Now we need to set our env file. Go to the root of your project and execute this command.
```sh
cp .env.example .env
```
And now you should provide **.env** file all the necessary environment variables:

#
**MYSQL:**
>DB_CONNECTION=mysql

>DB_HOST=127.0.0.1

>DB_PORT=3306

>DB_DATABASE=*****

>DB_USERNAME=*****

>DB_PASSWORD=*****


#
**MAILGUN:**
>MAILGUN_DOMAIN=******

>MAILGUN_SECRET=******

after setting up **.env** file, execute:
```sh
php artisan config:cache
```
in order to cache environment variables.

4\. Now execute in the root of you project following:
```sh
  php artisan key:generate
```
Which generates auth key.

##### Now, you should be good to go!


#
### Migration
if you've completed getting started section, then migrating database if fairly simple process, just execute:
```sh
php artisan migrate
```

#
### Running Unit tests
Running unit tests also is very simple process, just type in following command:

```sh
composer test
```

#
### Development

You can run Laravel's built-in development server by executing:

```sh
  php artisan serve
```

when working on JS you may run:

```sh
  npm run dev
```
it builds your js files into executable scripts.
If you want to watch files during development, execute instead:

```sh
  npm run watch
```
it will watch JS files and on change it'll rebuild them, so you don't have to manually build them.

#
### Project Structure

```bash
├─── app
│   ├─── Console
│   ├─── Events
│   ├─── Exceptions
│   ├─── Http
|   |   ├─── Controllers
|   |   ├─── Middleware
|   |   ├─── Requests
│   ├─── Providers
│   │... Models
├─── bootstrap
├─── config
├─── database
├─── lang
├─── public
├─── resources
├─── routes
├─── storage
├─── tests
- .env
- artisan
- composer.json
- package.json
- phpunit.xml
```

#
### Resources

- [Application Details](https://redberry.gitbook.io/assignment-iv-movie-quotes-1/)
- [Git commit rules](https://redberry.gitbook.io/resources/git-is-semantikuri-komitebi)
- [Drawsql](https://drawsql.app/teams/jarji-abuashvili/diagrams/epic-movie-quotes)
- [Hosted Website](https://api-epic-movie-quotes.jarjia.redberryinternship.ge/)
