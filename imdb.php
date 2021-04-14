<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>imdb api</title>
</head>
<body>
<form action="imdb.php" method="post">
    <label>
        <input type="text" name="search" placeholder="tt0111161">
    </label>
    <input type="submit" name="submit" value="search">
</form>
<?php
function finder($title)
{
    $title = str_replace(" ", '', $title);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://www.imdb.com/title/$title/");
    curl_setopt($curl, CURLOPT_TIMEOUT, 256);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

if (
    isset($_POST['submit']) and
    $_POST['search'] !== '' and
    !preg_match('<div id="error" class="error_code_404">', finder($_POST['search']), $test)
) :

    $movie = finder($_POST['search']);

    $show_link = preg_match('/\"url\":\s\"\/title\/(.*)\/\"/', $movie, $link) ? 'https://www.imdb.com/title/' . $link[1] . '/' : null;
    $show_name = preg_match('#<h1(.*)>(.*)&nbsp(.*)</h1>#Uis', $movie, $name) ? $name[2] : null;
    $show_date = preg_match('/\"datePublished\":\s\"(.*)-(.*)-(.*)\"/', $movie, $date) ? $date[1] : null;
    $show_poster = preg_match('/\"image\":\s\"(.*)\"/', $movie, $poster) ? $poster[1] : null;
    $show_rank = preg_match('#\"aggregateRating\":\s{(.*)\"ratingValue\":\s\"(.*)\"(.*)}#Uis', $movie, $rank) ? $rank[2] : null;
    $show_content_rating = preg_match('/"contentRating": "(.*)"/', $movie, $content_rating) ? $content_rating[1] : null;
    $show_director = preg_match('#\"director\":\s{(.*)\"name\":\s\"(.*)\"(.*)}#Uis', $movie, $director) ? $director[2] : null;
    $show_meta_score = preg_match('#<div class="metacriticScore(.*)">(.*)<span>(.*)</span>(.*)</div>#Uis', $movie, $meta_score) ? $meta_score[3] : null;
    $show_story = preg_match('#<h2>Storyline</h2>(.*)<div class="inline canwrap">(.*)<p>(.*)<span>(.*)</span>(.*)<em class="nobr">Written by#Uis', $movie, $story) ? str_replace("\n", '', trim($story[4])) : null;

    $show_awards = preg_match('#<span class="awards-blurb">(.*)<b>(.*)</b>(.*)</span>(.*)<span class="awards-blurb">(.*)</span>#Uis', $movie, $awards) ? str_replace("\n", '', $awards[2] . ' ' . $awards[5]) : null;
    if ($show_awards) :
        $show_awards = preg_replace('/\s+/', ' ', $show_awards);
        $show_awards = trim($show_awards);
    elseif (preg_match('#<span class="awards-blurb">(.*)</span>#Uis', $movie, $awards)) :
        $show_awards = preg_match('#<span class="awards-blurb">(.*)</span>#Uis', $movie, $awards) ? str_replace("\n", '', $awards[1]) : null;
        $show_awards = preg_replace('/\s+/', ' ', $show_awards);
        $show_awards = trim($show_awards);
    endif;

    $show_country = preg_match('#<div class="txt-block">(.*)<h4 class="inline">Country:</h4>(.*)</div>#Uis', $movie, $country) ? str_replace("\n", '', $country[2]) : null;
    if ($show_country) :
        $show_country = str_replace(' ', '', $show_country);
        while (preg_match('#<ahref="/search/title\?country_of_origin=(.*)&ref_=tt_dt_dt">#Uis', $show_country, $country)) {
            $delete = preg_match('#<ahref="/search/title\?country_of_origin=(.*)&ref_=tt_dt_dt">#Uis', $show_country, $country) ? $country[0] : null;
            $show_country = str_replace($delete, '', $show_country);
        }
        $show_country = str_replace('<spanclass="ghost">|</span>', ', ', $show_country);
        $show_country = str_replace("</a>", '', $show_country);
    endif;

    $show_language = preg_match('#<div class="txt-block">(.*)<h4 class="inline">Language:</h4>(.*)</div>#Uis', $movie, $language) ? str_replace("\n", '', $language[2]) : null;
    if ($show_language) :
        $show_language = str_replace(' ', '', $show_language);
        while (preg_match('#<ahref="/search/title\?title_type=feature&primary_language=(.*)&sort=moviemeter,asc&ref_=tt_dt_dt">#Uis', $show_language, $language)) {
            $delete = preg_match('#<ahref="/search/title\?title_type=feature&primary_language=(.*)&sort=moviemeter,asc&ref_=tt_dt_dt">#Uis', $show_language, $language) ? $language[0] : null;
            $show_language = str_replace($delete, '', $show_language);
        }
        $show_language = str_replace('<spanclass="ghost">|</span>', ', ', $show_language);
        $show_language = str_replace("</a>", '', $show_language);
    endif;

    if (preg_match('#<h4(.*)Release\sDate:(.*)\s(.*)\s\s(.*)<#Uis', $movie, $release))
        $full_date = preg_match('#<h4(.*)Release\sDate:(.*)\s(.*)\s\s(.*)<#Uis', $movie, $release) ? $release[3] : null;
    else
        $full_date = preg_match('/\"datePublished\":\s\"(.*)\"/', $movie, $f_date) ? $f_date[1] : null;

    $show_time_1 = preg_match('#datetime="PT(.*)M">(\s*)([0-9a-z 0-9a-z]*)(\s*)<#Uis', $movie, $time) ? $time[1] : null;
    if ($show_time_1) $show_time = preg_match('/\b(.*)/', $time[3], $time) ? $time[0] : null;
    else $show_time = null;

    if (preg_match('#<div\sclass="summary_text">(.*)</div>#Uis', $movie, $detail))
        $show_detail = preg_match('/\b(.*)/', $detail[1], $detail) ? $detail[0] : null;
    else $show_detail = null;

    $show_genre = preg_match('#\"genre\":\s(.*)"contentRating"#Uis', $movie, $genre) ? $genre[1] : null;
    if ($show_genre) {
        $show_genre = str_replace(' ', '', $show_genre);
        $show_genre = str_replace("\n", '', $show_genre);
        $show_genre = str_replace("\t", '', $show_genre);
        $show_genre = str_replace("\"", '', $show_genre);
        $show_genre = str_replace("[", '', $show_genre);
        $show_genre = str_replace("]", '', $show_genre);
        $show_genre = substr_replace($show_genre, "", -1);
        $show_genre = explode(",", $show_genre);
    }

    $x = 0; ?>
    <p>Link: <?php echo '<a target="_blank" href="' . $show_link . '">' . '<mark>' . $show_link . '</mark></a>'; ?></p>
    <p>Name: <?php echo '<mark>' . $show_name . " ($show_date)" . '</mark>'; ?></p>
    <p>Rank: <?php echo '<mark>' . $show_rank . '</mark>'; ?></p>
    <p>Meta Score: <?php echo '<mark>' . $show_meta_score . '</mark>'; ?></p>
    <p>Language: <?php echo '<mark>' . $show_language . '</mark>'; ?></p>
    <p>Country: <?php echo '<mark>' . $show_country . '</mark>'; ?></p>
    <p>Runtime: <?php echo '<mark>' . $show_time . " ($show_time_1 min)" . '</mark>'; ?></p>
    <p>Release Date: <?php echo '<mark>' . $full_date . '</mark>'; ?></p>
    <p>Detail: <?php echo '<mark>' . $show_detail . '</mark>'; ?></p>
    <p>Content Rating: <?php echo '<mark>' . $show_content_rating; ?></p>
    <p>Poster: <?php echo '<a target="_blank" href="' . $show_poster . '">' . '<mark>' . 'Link' . '</mark></a>'; ?></p>
    <p>Genre: <?php echo '<mark>';
        foreach ($show_genre as $genre) {
            $x++;

            if ($x == count($show_genre)) {
                echo "$genre";
            } else {
                echo "$genre" . ", ";
            }
        }
        echo '</mark>'; ?></p>
    <p>Director: <?php echo '<mark>' . $show_director . '</mark>'; ?></p>
    <p>Awards: <?php echo '<mark>' . $show_awards . '</mark>'; ?></p>
    <p>Story: <?php echo '<mark>' . $show_story . '</mark>'; ?></p>
<?php else : ?>
    <p>Type a valid title like:
        <mark>tt0111161</mark>
    </p>
<?php endif; ?>
</body>
</html>