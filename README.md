PimFrame
========
PHP RESTful API Framework

Basic PHP Framework Using Pimple Dependency Injection Container

+ http://pimple.sensiolabs.org/

Create your own Controller
------

Create Controller in the folder /application/controllers/, method name can either add http verbs with underscore after the name or no method name. The former case will check the verb and call corresponding method while the latter case will match all the verbs.

/application/controllers/UserController.php
```
<?php
class UserController {

  public function login_post() {
    return $this->UserModel->login(
      $this->request->post('login'),
      $this->request->post('pass')
    );
  }
  
  public function generic() {
    return 'generic';
  }

}
```

Model
------

Create Your Model in the folder /application/models

/application/models/UserModel.php
```
<?php
class UserModel {

  public function login($login, $pass) {
    $query = $this->database->execute('...', array($login, $pass));
    return $query->num_rows() > 0;
  }

}
```

In-Apps Usage
------

/testing.php
```
require('index.php');

//testing purpose
$apps->request->clear();
$apps->request->set_verb('POST');
$apps->request->set_post('login', 'username');
$apps->request->set_post('pass', 'password');

//login result
$result = $apps->run('user/login');

//return 'generic'
$result = $apps->run('user/generic');
```

RESTful API Call
------

Output will be in json format.

POST: /user/login
```
{...}
```

GET: /user/generic
```
"generic"
```

Advance Usage
------

```
$apps->database->onetomany('student', 'course');
$apps->security->hash('password');
```
