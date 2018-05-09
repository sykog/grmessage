<?php

    class Student
    {
        protected $username;
        protected $password;
        protected $phone;
        protected $carrier;
        protected $program;

        /**
         * Student constructor.
         * @param $email student email
         * @param $password pasword
         * @param $phone phone number
         * @param $carrier mobile carrier
         * @parm $program academic program
         * @return void
         */
        function __construct($email, $password, $phone, $carrier, $program)
        {
            $this->email = $email;
            $this->password = $password;
            $this->phone = $phone;
            $this->carrier = $carrier;
            $this->program = $program;
        }

    }