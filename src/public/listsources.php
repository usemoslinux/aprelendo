<?php
/**
 * Copyright (C) 2019 Pablo Castagnino
 *
 * This file is part of aprelendo.
 *
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

use Aprelendo\PopularSources;
use Aprelendo\Language;

try {
    $user_id = (int)$user->id;
    $lang_id = (int)$user->lang_id;
    $language = new Language($pdo, $user_id);
    $language->loadRecordById($lang_id);
    $selected_level = (int)$language->level;

    $pop_sources = new PopularSources($pdo);
    $sources = $pop_sources->getAllByLang($user->lang);
    $youtube_video_count = $pop_sources->getYoutubeVideoCountByLang($user->lang);
    $sources = includeYoutubeSourceInTopList($sources, $youtube_video_count);
    $insights = $pop_sources->getInsightsByLang($user->lang);
    echo printSources($sources, $insights, $selected_level);
} catch (\Exception $e) {
    $html = <<<'HTML_UNEXPECTED_ERROR'
    <div id="alert-box" class="alert alert-danger">
        <div class="alert-flag fs-5"><i class="bi bi-exclamation-circle-fill"></i>Oops!</div>
        <div class="alert-msg">
            <p>There was an unexpected error trying to show sources for this language.</p>
        </div>
    </div>
    HTML_UNEXPECTED_ERROR;
    echo $html;
}

/**
 * Injects a synthetic youtube.com source into the top list.
 *
 * The source count is based on the number of shared video texts (type = 5)
 * in the selected language. The final list keeps ranking by times used and
 * preserves the same maximum size returned by popular_sources.
 *
 * @param array $sources
 * @param int $youtube_video_count
 * @return array
 */
function includeYoutubeSourceInTopList(array $sources, int $youtube_video_count): array
{
    if ($youtube_video_count <= 0) {
        return $sources;
    }

    $show_count = max(1, (int)PopularSources::SHOW_COUNT);
    $filtered_sources = [];

    foreach ($sources as $source) {
        $raw_domain = (string)($source['domain'] ?? '');
        $normalized_domain = normalizeDomainForInsights($raw_domain);

        if (in_array($normalized_domain, ['youtube.com', 'youtu.be', 'm.youtube.com'], true)) {
            continue;
        }

        $filtered_sources[] = $source;
    }

    $filtered_sources[] = [
        'domain' => 'youtube.com',
        'times_used' => $youtube_video_count
    ];

    usort($filtered_sources, function (array $left_source, array $right_source): int {
        $left_count = (int)($left_source['times_used'] ?? 0);
        $right_count = (int)($right_source['times_used'] ?? 0);

        if ($left_count === $right_count) {
            $left_domain = mb_strtolower((string)($left_source['domain'] ?? ''));
            $right_domain = mb_strtolower((string)($right_source['domain'] ?? ''));

            return $left_domain <=> $right_domain;
        }

        return $right_count <=> $left_count;
    });

    if (count($filtered_sources) > $show_count) {
        return array_slice($filtered_sources, 0, $show_count);
    }

    return $filtered_sources;
}

/**
 * Prints the popular sources list and language insights.
 *
 * @param array $sources
 * @param array $insights
 * @param int $selected_level
 * @return string
 */
function printSources(array $sources, array $insights, int $selected_level): string
{
    $html = <<<HTML_INFO_SOURCES
        <div class="alert alert-info">These are the most popular sources for the currently selected language. They
            are probably a good starting place to find new content to practice. Remember to use our <a
            href="/extensions" class="alert-link" target="_blank" rel="noopener noreferrer">extensions</a> to add
            articles from these or other sources to your Aprelendo library.
        </div>
    HTML_INFO_SOURCES;

    $html .= printInsights($insights);

    if (!isset($sources) || empty($sources)) {
        $html .= <<<HTML_EMPTY_LIBRARY
            <div id="alert-box" class="alert alert-warning">
                <div class="alert-flag fs-5">
                    <i class="bi bi-stars"></i> Nothing here… yet!
                </div>
                <div class="alert-msg">
                    <p>Hmm, that's unusual. We couldn't find any popular sources for the selected language.</p>
                    <p>
                        This probably means that there are no <a href="/sharedtexts" class="alert-link">shared texts</a>
                        available yet for this language. Try exploring another language for now, or be the first to
                        share something and help grow the collection!
                    </p>
                </div>
            </div>
        HTML_EMPTY_LIBRARY;
        return $html;
    }

    $html .= '<div id="list-group-popular-sources" class="list-group">';

    foreach ($sources as $source) {
        $raw_domain = (string)($source['domain'] ?? '');
        $normalized_domain = normalizeDomainForInsights($raw_domain);
        $domain = htmlspecialchars($raw_domain, ENT_QUOTES, 'UTF-8');
        $times_used = (int)($source['times_used'] ?? 0);
        $domain_insights = $insights['domain_insights'][$raw_domain]
            ?? $insights['domain_insights'][$normalized_domain]
            ?? [];
        $domain_badges = printDomainBadges($domain_insights, $selected_level);

        $html .= <<<HTML_SOURCE
        <a href='//{$domain}' target='_blank' rel='noopener noreferrer'
            class='list-group-item d-flex justify-content-between align-items-center list-group-item-action'>
            <div>
                <div class='text-primary fw-semibold'>{$domain}</div>
                {$domain_badges}
            </div>
            <span class='badge bg-secondary badge-pill ms-2'>{$times_used}</span>
        </a>
        HTML_SOURCE;
    }

    $html .= '</div>';

    return $html;
}

/**
 * Prints summary insights for shared texts in the selected language.
 *
 * @param array $insights
 * @return string
 */
function printInsights(array $insights): string
{
    $total_texts = (int)($insights['total_texts'] ?? 0);
    $with_audio_percentage = (int)($insights['with_audio_percentage'] ?? 0);
    $avg_word_count = (int)($insights['avg_word_count'] ?? 0);
    $type_distribution = (array)($insights['type_distribution'] ?? []);
    $level_distribution = (array)($insights['level_distribution'] ?? []);

    if ($total_texts === 0) {
        return '';
    }

    $type_badges = printDistributionBadges($type_distribution, 'bg-primary-subtle text-primary-emphasis border');
    $level_badges = printDistributionBadges($level_distribution, 'bg-success-subtle text-success-emphasis border');
    $total_texts_label = number_format($total_texts);
    $avg_word_count_label = number_format($avg_word_count);

    return <<<HTML_INSIGHTS
    <div class="card border border-secondary-subtle bg-light mb-3">
        <div class="card-body py-3">
            <h2 class="h6 mb-2">Community insights</h2>
            <p class="small text-muted mb-3">
                Based on {$total_texts_label} shared texts in this language.
                With source audio provided: {$with_audio_percentage}%.
                Typical length: {$avg_word_count_label} words.
            </p>
            <div class="mb-2">
                <div class="small fw-bold mb-1">Text types</div>
                {$type_badges}
            </div>
            <div>
                <div class="small fw-bold mb-1">Difficulty levels</div>
                {$level_badges}
            </div>
        </div>
    </div>
    HTML_INSIGHTS;
}

/**
 * Prints a row of percentage badges for a distribution.
 *
 * @param array $distribution
 * @param string $badge_css_class
 * @return string
 */
function printDistributionBadges(array $distribution, string $badge_css_class): string
{
    if (empty($distribution)) {
        return '<span class="small text-muted">No data available</span>';
    }

    $badges = '';
    foreach ($distribution as $item) {
        $label = htmlspecialchars((string)($item['label'] ?? ''), ENT_QUOTES, 'UTF-8');
        $percentage = (float)($item['percentage'] ?? 0);
        $count = (int)($item['count'] ?? 0);
        $percentage_label = number_format($percentage, 1);
        $count_label = number_format($count);

        $badges .= "<span class='badge rounded-pill {$badge_css_class} me-1 mb-1'>{$label}: {$percentage_label}% ({$count_label})</span>";
    }

    return $badges;
}

/**
 * Prints source-level badges that help users choose what to open next.
 *
 * @param array $domain_insights
 * @param int $selected_level
 * @return string
 */
function printDomainBadges(array $domain_insights, int $selected_level): string
{
    if (empty($domain_insights)) {
        return '';
    }

    $dominant_type = htmlspecialchars((string)($domain_insights['dominant_type'] ?? 'Mixed'), ENT_QUOTES, 'UTF-8');
    $level_shares = (array)($domain_insights['level_shares'] ?? []);
    $beginner_share = (int)($level_shares['beginner'] ?? 0);
    $intermediate_share = (int)($level_shares['intermediate'] ?? 0);
    $advanced_share = (int)($level_shares['advanced'] ?? 0);
    $audio_share = (int)($domain_insights['audio_share'] ?? 0);
    $audio_badge = $audio_share > 0
        ? "<span class='badge rounded-pill bg-info-subtle text-info-emphasis border border-info-subtle mb-1'>{$audio_share}% with source audio</span>"
        : '';

    $level_badges = printSelectedLevelBadges($selected_level, $beginner_share, $intermediate_share, $advanced_share);

    return <<<HTML_DOMAIN_BADGES
    <div class="small mt-1 d-flex flex-wrap gap-1">
        <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis border border-primary-subtle mb-1">Mostly {$dominant_type}</span>
        {$level_badges}
        {$audio_badge}
    </div>
    HTML_DOMAIN_BADGES;
}

/**
 * Prints difficulty badges based on the user's selected language level.
 *
 * If selected level is "All" (0), prints percentages for all levels.
 * Otherwise, prints only the percentage for the selected level.
 *
 * @param int $selected_level
 * @param int $beginner_share
 * @param int $intermediate_share
 * @param int $advanced_share
 * @return string
 */
function printSelectedLevelBadges(
    int $selected_level,
    int $beginner_share,
    int $intermediate_share,
    int $advanced_share
): string {
    if ($selected_level === 1) {
        return "<span class='badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle mb-1'>{$beginner_share}% Beginner</span>";
    }

    if ($selected_level === 2) {
        return "<span class='badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle mb-1'>{$intermediate_share}% Intermediate</span>";
    }

    if ($selected_level === 3) {
        return "<span class='badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle mb-1'>{$advanced_share}% Advanced</span>";
    }

    return
        "<span class='badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle mb-1'>{$beginner_share}% Beginner</span>" .
        "<span class='badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle mb-1'>{$intermediate_share}% Intermediate</span>" .
        "<span class='badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle mb-1'>{$advanced_share}% Advanced</span>";
}

/**
 * Normalizes a domain key for consistent insights lookup.
 *
 * @param string $domain
 * @return string
 */
function normalizeDomainForInsights(string $domain): string
{
    $normalized_domain = mb_strtolower(trim($domain));
    $normalized_domain = preg_replace('/^https?:\/\//', '', $normalized_domain);
    $normalized_domain = explode('/', $normalized_domain)[0];

    if (preg_match('/^www[0-9]*\./', $normalized_domain)) {
        $normalized_domain = preg_replace('/^www[0-9]*\./', '', $normalized_domain);
    }

    return $normalized_domain;
}
