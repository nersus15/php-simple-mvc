<?php
require_once './constant.php';

foreach (glob("./thirdParty/dotenv/src/josegonzalez/Dotenv/filter/*.php") as $filename)
    require_once $filename;

require_once './thirdParty/m1/env/src/Parser.php';

foreach (glob("./thirdParty/m1/env/src/Exception/*.php") as $filename)
    require_once $filename;
foreach (glob("./thirdParty/m1/env/src/Helper/*.php") as $filename)
    require_once $filename;

foreach (glob("./thirdParty/m1/env/src/Parser/*.php") as $filename)
    require_once $filename;

require_once './thirdParty/dotenv/src/josegonzalez/Dotenv/Loader.php';
require_once './thirdParty/dotenv/src/josegonzalez/Dotenv/Expect.php';

require_once './helpers/core.php';
require_once './helpers/utils.php';

require_once './helpers/query_builder.php';