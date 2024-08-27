<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title>Daily Algorithm</title>
        <link>https://daily-algorithm.com</link>
        <description>Daily Algorithm RSS Feed</description>
        <pubDate><?= convertTimestamp($algorithms[0]["date"]) ?></pubDate>

        <?php foreach($algorithms as $algorithm): ?>
            <item>
                <title><?= htmlspecialchars($algorithm["name"]) ?></title>
                <link><?= "https://daily-algorithm.com/prev/" . date("Y-m-d", $algorithm["date"]) ?></link>
                <description><?= htmlspecialchars($algorithm["content"][0]["content"]) ?></description>
                <content:encoded>
                    <![CDATA[
                        <?php foreach($algorithm["content"] as $content): ?>
                            <h4><?= htmlspecialchars($content["title"]) ?></h4>
                            <?php if ($content["type"] === "code"): ?>
                                <p><pre><code><?= htmlspecialchars($content["content"]) ?></code></pre></p>
                            <?php endif; ?>
                            <?php if ($content["type"] === "text"): ?>
                                <p><?= htmlspecialchars($content["content"]) ?></p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    ]]>
                </content:encoded>
                <pubDate><?= convertTimestamp($algorithm["date"]) ?></pubDate>
            </item>
        <?php endforeach; ?>
    </channel>
</rss>
<?php
function convertTimestamp($timestamp) {
    $date = new DateTime("@{$timestamp}");
    return $date->format("Y-m-d\TH:i:s");
}