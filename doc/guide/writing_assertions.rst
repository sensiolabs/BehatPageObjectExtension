Writing assertions
==================

Page objects are our interface to the web pages. We still need context files
though, not only to call the page objects, but also to verify expectations.

Traditionally we'd want to throw exceptions if expectations are not met.
The difference is we'd ask a page object to provide needed page details
instead of retrieving them ourselves in the context file:

    .. code-block:: php

        use Behat\Behat\Context\Context;

        class ConferenceContext implements Context
        {
            private $conferenceList;

            public function __construct(ConferenceList $conferenceList)
            {
                $this->conferenceList = $conferenceList;
            }

            /**
             * @Then /^(?:|I )should not be able to enrol to (?:|the )"(?P<conferenceName>[^"]*)" conference$/
             */
            public function iShouldNotBeAbleToEnrolToTheConference($conferenceName)
            {
                if ($this->conferenceList->hasEnrolmentButtonFor($conferenceName)) {
                    $message = sprintf('Did not expect to find an enrollment button for the "%s" conference.', $conferenceName);

                    throw new \LogicException($message);
                }
            }
        }

Our page object could look like the following:

    .. code-block:: php

        namespace Page;

        class ConferenceList extends Page
        {
            public function hasEnrolmentButtonFor($conferenceName)
            {
                $conferenceSlug = str_replace(' ', '-', strtolower($conferenceName));
                $button = $this->find('css', sprintf('#enrol-%s', $conferenceSlug));

                return !is_null($button);
            }
        }

We could go one step fruther in making our life easier by using phpspec
matchers available through the
`expect() helper <https://github.com/BossaConsulting/phpspec2-expect>`_:

    .. code-block:: php

        /**
         * @Then /^(?:|I )should not be able to enrol to (?:|the )"(?P<conferenceName>[^"]*)" conference$/
         */
        public function iShouldNotBeAbleToEnrolToTheConference($conferenceName)
        {
            expect($this->getPage('Conference list'))->notToHaveEnrolmentButtonFor($conferenceName);
        }

To use the `expect() helper <https://github.com/BossaConsulting/phpspec2-expect>`_,
we need to install it first. Best way to do this is by adding it to the
``composer.json``:

    .. code-block:: js

        "require-dev": {
            "bossa/phpspec2-expect": "~1.0"
        }
