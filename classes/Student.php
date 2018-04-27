<?php

    class Student
    {
        protected $username;
        protected $password;
        protected $phone;
        protected $carrier;

        /**
         * Student constructor.
         * @param $email student email
         * @param $password pasword
         * @param $phone phone number
         * @param $carrier mobile carrier
         * @return void
         */
        function __construct($email, $password, $phone, $carrier)
        {
            $this->email = $email;
            $this->password = $password;
            $this->phone = $phone;
            $this->carrier = carrier;
        }

    }