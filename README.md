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

Database
------

```
$apps->database->onetomany('student', 'course');  #return all the students with all courses taking by students
/*
structure returned:
array(
 [0] =>
 array(
   "student_id" => 1,
   "other_field" => ...,
   "course" => array(
     [0] =>
     array(
       "course_id" => 1,
       "other_field" => ...,
     ),
     [1] =>
     array(
       ...
     )
   )
 ),
 [1] =>
 array(
   ...
 )
)
*/
$apps->database->onetomany('student', 'course', 'student_id=3'); #return a stduent with id=3 with all courses taking by that student
```

Security
------

```
$apps->security->hash('password');
```

Session
------

```
$apps->session->set('key', '12345');
$apps->session->get('key');
$apps->session->remove('key');
```

Request
------

```
$apps->request->get(); #return all gets
$apps->request->post(); #return all posts
$apps->request->get('key'); #return $_GET['key'] or false if no value
$apps->request->post('key'); #return $_POST['key'] or false if no value
```

Debug
------

```
$apps->debug->trace();
```

Config
------

```
```

i18n
------

```
```
