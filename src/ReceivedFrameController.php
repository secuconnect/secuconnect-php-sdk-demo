<?php

namespace Secuconnect\Demo;

class ReceivedFrameController implements \Secuconnect\Client\STOMP\Comunication\ReceivedFrameControllerInterface
{
    public function process($frame)
    {
        print_r($frame);

        /*
         * SAMPLE
         *
            StompFrame Object
            (
                [command] => MESSAGE
                [headers] => Array
                    (
                        [subscription] => /temp-queue/main
                        [destination] => /queue/amq.gen-JZ9jenQoyZLxgNiL1chpVQ
                        [message-id] => T_/temp-queue/main@@session-Xx2lrMYsoqm3OmSPUbJt3g@@2
                        [redelivered] => false
                        [content-type] => application/x-json
                        [content-length] => 213
                    )

                [body] => {"object":"event.actions","id":"EAC_C97EHS7SMT6UWZGTTVCDYX52HDDT4J","created":"2019-03-07T15:01:33+01:00","target":"smart.transactions","type":"startPayment","data":{"amount":60,"currency":"EUR","protocol":"zvt"}}
            )
         */


        /*
         * SAMPLE
         *
            StompFrame Object
            (
                [command] => MESSAGE
                [headers] => Array
                    (
                        [subscription] => /temp-queue/main
                        [destination] => /queue/amq.gen-JZ9jenQoyZLxgNiL1chpVQ
                        [message-id] => T_/temp-queue/main@@session-Xx2lrMYsoqm3OmSPUbJt3g@@1
                        [redelivered] => false
                        [correlation-id] => 5c812436ce778-1933218026-1551967286
                        [content-type] => application/x-json
                        [content-length] => 38
                    )

                [body] => {"status":"ok","data":{"result":true}}
            )
         */
    }
}
