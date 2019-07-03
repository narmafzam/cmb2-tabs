<?php

use Tab\Tab;

function getTabInstance()
{
    return Tab::getInstance();
}

getTabInstance()->init();