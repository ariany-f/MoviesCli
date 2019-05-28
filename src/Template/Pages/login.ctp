<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Http\Exception\NotFoundException;

$this->layout = 'login';

?>
<div class="container">
    <div class="row">
    <div class="col-lg-4 col-md-6 ml-auto mr-auto">
        <div class="card card-login">
        <form class="form" method="" action="">
            <div class="card-header card-header-primary text-center">
            <h4 class="card-title">Login</h4>
            <div class="social-line">
                <a href="#pablo" class="btn btn-just-icon btn-link">
                <i class="fa fa-facebook-square"></i>
                </a>
                <a href="#pablo" class="btn btn-just-icon btn-link">
                <i class="fa fa-twitter"></i>
                </a>
                <a href="#pablo" class="btn btn-just-icon btn-link">
                <i class="fa fa-google-plus"></i>
                </a>
            </div>
            </div>
            <p class="description text-center">Or Be Classical</p>
            <div class="card-body">
            <div class="input-group">
                <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="material-icons">face</i>
                </span>
                </div>
                <input type="text" class="form-control" placeholder="First Name...">
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="material-icons">mail</i>
                </span>
                </div>
                <input type="email" class="form-control" placeholder="Email...">
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="material-icons">lock_outline</i>
                </span>
                </div>
                <input type="password" class="form-control" placeholder="Password...">
            </div>
            </div>
            <div class="footer text-center">
            <a href="#pablo" class="btn btn-primary btn-link btn-wd btn-lg">Get Started</a>
            </div>
        </form>
        </div>
    </div>
    </div>
</div>