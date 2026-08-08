<?php

/**
 * Demand signals for specialist-module validation.
 *
 * ---------------------------------------------------------------------------
 * VENDORED FILE — DO NOT EDIT IN PLACE.
 *
 * Canonical copy lives in divi-carousel-pro/includes/shared/v1/. Copied into
 * divi-carousel-free by `npm run sync:shared`; CI fails if they diverge.
 * ---------------------------------------------------------------------------
 *
 * The roadmap's final phase gates a set of specialist modules — Comparison,
 * Map-Synced, Shoppable Video, Shop-the-Look, Listing — behind a rule:
 * "Do not build every specialist module automatically. Require evidence from
 * feature votes, support requests, sales conversations, or integration
 * partners."
 *
 * That phase cannot be completed by writing modules; building them without the
 * evidence violates the rule rather than satisfying it. What it needs is a way
 * to gather the evidence. This is that mechanism.
 *
 * Deliberately local-only. Nothing is transmitted anywhere: signals are stored
 * in a site option and surfaced in the plugin's own admin screen. Sending
 * product analytics off-site is a consent decision for the site owner, not
 * something a plugin should assume — see the roadmap's own metrics section,
 * which requires "appropriate consent and clear documentation".
 *
 * @package DiviCarousel\Shared
 */

namespace DiviCarouselShared\V1;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists(__NAMESPACE__ . '\DemandSignals')) {

    /**
     * Records which capabilities are actually being used or asked for.
     */
    class DemandSignals
    {
        const VERSION = '1.0.0';
        const OPTION  = 'divi_carousel_demand_signals';

        /**
         * Specialist modules awaiting demand evidence, from the roadmap.
         *
         * @return array slug => label
         */
        public static function candidates(): array
        {
            return [
                'comparison'     => 'Comparison Carousel',
                'map_synced'     => 'Map-Synced Carousel',
                'shoppable_video' => 'Shoppable Video Carousel',
                'shop_the_look'  => 'Shop-the-Look Carousel',
                'listing'        => 'Listing Carousel',
                'audio_podcast'  => 'Audio / Podcast Carousel',
                'stories'        => 'Mobile Stories Carousel',
                'event'          => 'Event Carousel',
            ];
        }

        /**
         * Record one signal.
         *
         * @param string $candidate Slug from candidates().
         * @param string $kind      request|usage|support|sales|partner
         * @param string $note      Optional free text, e.g. a ticket reference.
         */
        public static function record(string $candidate, string $kind = 'request', string $note = ''): bool
        {
            if (!array_key_exists($candidate, self::candidates())) {
                return false;
            }

            $allowed = ['request', 'usage', 'support', 'sales', 'partner'];
            if (!in_array($kind, $allowed, true)) {
                $kind = 'request';
            }

            $signals = get_option(self::OPTION, []);
            if (!is_array($signals)) {
                $signals = [];
            }

            if (!isset($signals[$candidate])) {
                $signals[$candidate] = ['total' => 0, 'by_kind' => [], 'notes' => []];
            }

            $signals[$candidate]['total']++;
            $signals[$candidate]['by_kind'][$kind] = ($signals[$candidate]['by_kind'][$kind] ?? 0) + 1;

            $note = sanitize_text_field($note);
            if ('' !== $note) {
                // Keep the most recent notes only; this is a signal store, not
                // a ticket system.
                $signals[$candidate]['notes'][] = $note;
                $signals[$candidate]['notes'] = array_slice($signals[$candidate]['notes'], -20);
            }

            return update_option(self::OPTION, $signals, false);
        }

        /**
         * All recorded signals, highest first.
         *
         * @return array
         */
        public static function all(): array
        {
            $signals = get_option(self::OPTION, []);
            if (!is_array($signals)) {
                return [];
            }

            uasort($signals, static function ($a, $b) {
                return ($b['total'] ?? 0) <=> ($a['total'] ?? 0);
            });

            return $signals;
        }

        /**
         * Has a candidate met the threshold for building?
         *
         * The threshold is a starting point, not a law — it exists so the
         * decision is made against a number rather than a hunch, and it is
         * filterable because the right number depends on install base.
         */
        public static function is_validated(string $candidate): bool
        {
            $threshold = (int) apply_filters('divi_carousel_demand_threshold', 25, $candidate);
            $signals   = self::all();

            return ($signals[$candidate]['total'] ?? 0) >= $threshold;
        }

        /**
         * Candidates that have met the threshold.
         *
         * @return array slug => label
         */
        public static function validated(): array
        {
            $out = [];

            foreach (self::candidates() as $slug => $label) {
                if (self::is_validated($slug)) {
                    $out[$slug] = $label;
                }
            }

            return $out;
        }

        /**
         * A readable summary for the admin screen.
         *
         * @return array
         */
        public static function report(): array
        {
            $signals   = self::all();
            $threshold = (int) apply_filters('divi_carousel_demand_threshold', 25, '');
            $rows      = [];

            foreach (self::candidates() as $slug => $label) {
                $total = (int) ($signals[$slug]['total'] ?? 0);

                $rows[] = [
                    'slug'      => $slug,
                    'label'     => $label,
                    'total'     => $total,
                    'threshold' => $threshold,
                    'validated' => $total >= $threshold,
                    'by_kind'   => $signals[$slug]['by_kind'] ?? [],
                ];
            }

            return $rows;
        }
    }
}
