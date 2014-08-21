Introduction to page objects
============================

**Page object** encapsulates all the dirty details of a user interface.
Instead of messing with the page internals in our context files, we'd
rather ask a page object to do this for us:

    .. code-block:: php

        <?php

        /**
         * @Given /^(?:|I )change my password$/
         */
        public function iChangeMyPassword()
        {
            // $page = get page...
            $page->login('kuba', '123123')
               ->changePassword('abcabc')
               ->logout();
        }

Page objects hide the UI and expose clean services (like login or
changePassword), which can be used in the context classes.
On one side, page objects are facing the developer by providing him a clean
interface to interact with the pages. On the other side, they're facing the HTML,
being the only thing that has knowledge about a structure of a page.

The idea is we end up with much cleaner context classes and avoid duplication.
Since page objects group similar concepts together, they are easier to maintain.
For example, instead of having a concept of a login form in multiple contexts,
we'd only store it in one page object.
