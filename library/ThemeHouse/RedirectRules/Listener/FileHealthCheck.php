<?php

class ThemeHouse_RedirectRules_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/ThemeHouse/RedirectRules/ControllerAdmin/RedirectRule.php' => '72de94cef83c32b7e76e9b3e526ea35c',
                'library/ThemeHouse/RedirectRules/DataWriter/RedirectRule.php' => '51dafdb6b5180dbc1d2131626fb19521',
                'library/ThemeHouse/RedirectRules/Install/Controller.php' => '719905c504070fde21c43f3f08242864',
                'library/ThemeHouse/RedirectRules/Listener/FrontControllerPreView.php' => 'ecfc2bbbfa126c194e64e8ae8acd0eca',
                'library/ThemeHouse/RedirectRules/Listener/TemplatePostRender.php' => '44fa9c2bc844baa9a92fd2b5dba393c0',
                'library/ThemeHouse/RedirectRules/Model/RedirectRule.php' => '4c9d8e5d7f0a2db9666edd70e5d1e6d7',
                'library/ThemeHouse/RedirectRules/Route/PrefixAdmin/RedirectRules.php' => '601586d0261a064e2b1793b005d8ba77',
                'library/ThemeHouse/Install.php' => '18f1441e00e3742460174ab197bec0b7',
                'library/ThemeHouse/Install/20151109.php' => '2e3f16d685652ea2fa82ba11b69204f4',
                'library/ThemeHouse/Deferred.php' => 'ebab3e432fe2f42520de0e36f7f45d88',
                'library/ThemeHouse/Deferred/20150106.php' => 'a311d9aa6f9a0412eeba878417ba7ede',
                'library/ThemeHouse/Listener/ControllerPreDispatch.php' => 'fdebb2d5347398d3974a6f27eb11a3cd',
                'library/ThemeHouse/Listener/ControllerPreDispatch/20150911.php' => 'f2aadc0bd188ad127e363f417b4d23a9',
                'library/ThemeHouse/Listener/InitDependencies.php' => '8f59aaa8ffe56231c4aa47cf2c65f2b0',
                'library/ThemeHouse/Listener/InitDependencies/20150212.php' => 'f04c9dc8fa289895c06c1bcba5d27293',
                'library/ThemeHouse/Listener/FrontControllerPreView.php' => '708878ad7afd1b975bd16e438efdb3b6',
                'library/ThemeHouse/Listener/FrontControllerPreView/20150106.php' => 'c35679c2d50e2791d97e1e7b8d3e7e5a',
                'library/ThemeHouse/Listener/Template.php' => '0aa5e8aabb255d39cf01d671f9df0091',
                'library/ThemeHouse/Listener/Template/20150106.php' => '8d42b3b2d856af9e33b69a2ce1034442',
                'library/ThemeHouse/Listener/TemplatePostRender.php' => 'b6da98a55074e4cde833abf576bc7b5d',
                'library/ThemeHouse/Listener/TemplatePostRender/20150106.php' => 'efccbb2b2340656d1776af01c25d9382',
            ));
    }
}