<?php
date_default_timezone_set('Asia/Hong_Kong');
function getTimePassed($datetime)
{
    $now = new DateTime();
    $datetime = new DateTime($datetime);
    $difference = $now->diff($datetime);

    if ($difference->y > 0)
        return $datetime->format('M j Y');
    elseif ($difference->m > 0)
        return $datetime->format('M j');
    elseif ($difference->days > 0)
        return $difference->days . 'd';
    elseif ($difference->h > 0)
        return $difference->h . 'h';
    elseif ($difference->i > 0)
        return $difference->i . 'm';
    else
        return 'Just now';
}