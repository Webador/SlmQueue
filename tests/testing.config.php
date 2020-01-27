<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

use SlmQueueTest\Asset\SimpleQueue;

return [
    'service_manager' => [
        'factories' => [
            'SlmQueueTest\Asset\SimpleWorker' => 'SlmQueue\Factory\WorkerFactory',
        ],
    ],
    'slm_queue' => [
        /**
         * Queues config
         */
        'queue_manager' => [
            'factories' => [
                'basic-queue' => function ($locator) {
                    /*
                     * avoid calling deprecated ServiceLocator on SM3
                     */
                    if ($locator->has('SlmQueue\Job\JobPluginManager')) {
                        $parentLocator = $locator;
                    } else {
                        $parentLocator = $locator->getServiceLocator();
                    }
                    $jobPluginManager = $parentLocator->get('SlmQueue\Job\JobPluginManager');

                    return new SimpleQueue('basic-queue', $jobPluginManager);
                },
            ],
        ],
    ],
];
