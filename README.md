PimFrame
========
PHP Framework - Framework can be used inside your web application and RESTful API at the same time.

Create your own Controller
------

Create Controller in the folder /application/controllers/, method name can either add http verbs with underscore after the name or no method name. The former case will check the verb and call corresponding method while the latter case will match all the verbs.

/application/controllers/UserController.php
```
<?php
class UserController extends IoC_Controller {

  public function __construct() {
    parent::__construct();
  }
	
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
class UserModel extends IoC_Model {

  public function __construct() {
    parent::__construct();
    $this->load->model('otherModel');
  }
  
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

Query with one to many relationship.
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

Smart Transaction Engine.
You can define nest transaction, all the rest will be handled by our framework. If a query is failed in some stage, all the things will be rollback automatically. If you forgot to end the transaction (trans_end), our framework will handle it too.
```
$this->database->trans_start();

... some query ...

$this->database->trans_start();

... some query ...

if(condition) $this->database->rollback();

$this->database->trans_end();

$this->database->trans_end();
```
Security
------

```
$apps->security->hash('password');
```

Session
------
Server session will be used.

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

Response
------

You can add custom defined parser and create your template engine to our framework. Before sending the page to clients, the body content will be processed by the parser. You can add as many parsers as you can.
```
$my_parser = function($body) {
	return preg_replace(some pattern, some replacement, $body);
};
$apps->response->add_parser($my_parser);
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
