# Mobile Application Subscription Managment API / Worker

iOS or Google mobile applications will be able to make in-app-purchase purchase / verification and current subscription
control using this API. On the worker side, the expire-dates of the existing active subscriptions in the database can be
updated via iOS or Google and updated their status and expire-dates.

## Installation & Run

#### Download project

```
 git@github.com:mfurkandogan/purchase.git
 ```

#### Database migration

Default db config : 
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=purchase_db
DB_USERNAME=root
DB_PASSWORD=
```
```
 php artisan migrate
 ```

#### Subscription Control

Records that started with this trigger, but passed the expire-date in the database but were not canceled, can be
verified one by one on iOS or Google mock platforms, according to the os value, and the values in the DB can be updated.

```
 php artisan subs:control
  ```

#### Run project

```
 php artisan serve
```

### Register

> POST    v1/register

##### Request

```
 {
    "uid":"device id",
    "appId":"application id",
    "language":"language",
    "os":"operation system"
}
```

##### Response

Success : HTTP Status 201

```

{
    "message": {
        "client_token": "client token key"
    }
}

```

Error : HTTP Status 400

```
{
    "message": [
        "This app was registered"
    ]
}

```

Error : HTTP Status 400

```

{
    "message": [
        {
            "uid": [
                "The uid field is required."
            ],
            "appId": [
                "The app id field is required."
            ],
            "language": [
                "The language field is required."
            ],
            "os": [
                "The os field is required."
            ]
        }
    ]
}
```

#### ! Bearer token is required for all endpoint in below. !

### Purchase

> POST    v1/purchase

##### Request

```
{
    "receipt" : "receipt"
}
```

##### Response
Success : HTTP Status 200
```
{
    "message": {
        "status": true,
        "expire_date": "2022-02-10 07:24:00"
    }
}
```
Error : HTTP Status 200
```
{
    "message": {
        "status": false
    }
}
```
Error : HTTP Status 500
```
{
    "message": [
        "Receipt not found!"
    ]
}
```

### checkSubscription

> POST    v1/checkSubscription

##### Response
Success : HTTP Status 200
```
{
    "message": {
        "status": true
    }
}
```
Error : HTTP Status 200
```
{
    "message": {
        "status": false
    }
}
```
Error : HTTP Status 401
