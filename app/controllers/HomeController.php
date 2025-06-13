<?php
class HomeController extends Controller {
    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
    }
    
    public function index() {
        $data = [
            'title' => 'Welcome to LAN Install',
            'content' => 'This is the home page of our application.'
        ];
        
        $this->view('home/index', $data);
    }
}
