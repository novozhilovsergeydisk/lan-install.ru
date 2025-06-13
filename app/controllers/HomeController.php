<?php
class HomeController extends Controller {
    public function index() {
        $data = [
            'title' => 'Welcome to LAN Install',
            'content' => 'This is the home page of our application.'
        ];
        
        $this->view('home/index', $data);
    }
}
