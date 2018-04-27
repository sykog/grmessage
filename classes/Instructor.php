<?php

    class Instructor {
        protected $email;
        protected $password;
        protected $confirm;
        protected $first;
        protected $last;
        protected $phone;
        protected $carrier;

        /**
         * Registration constructor.
         * @param $email email address
         * @param $password new password
         * @param $confirm confirm the password
         * @param $first first name
         * @param $last last name
         * @param $phone phone number
         * @param $carrier phone carrier
         */
        function __construct($email, $password, $confirm, $first, $last, $phone, $carrier)
        {
            $this->email = $email;
            $this->password = $password;
            $this->confirm = $confirm;
            $this->first = $first;
            $this->last = $last;
            $this->phone = $phone;
        }
    }