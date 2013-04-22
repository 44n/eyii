<?php

if(!EYii::includeDefaultComponent('AuthManager','user')){
	class AuthManager extends EAuthManager{}
}