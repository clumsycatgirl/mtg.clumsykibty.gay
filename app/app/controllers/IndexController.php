<?php

namespace App\Controllers;

use App\Models\CardidentifiersModel;
use App\Models\CardsModel;
use App\Models\DataClasses\Cardidentifiers;
use App\Models\DataClasses\Cards;
use App\Models\DataClasses\Tokenidentifiers;
use App\Models\DataClasses\Tokens;
use App\Models\TokenidentifiersModel;
use App\Models\TokensModel;
use Lib\Database\Database;
use Lib\Database\QueryBuilder;
use Lib\Database\QueryOperators;
use Lib\Systems\Controllers\Controller;
use Lib\Systems\Traits\CookieTrait;
use Lib\Systems\Traits\SessionTrait;
use Lib\Systems\Traits\ViewTrait;

class IndexController extends Controller {
    use ViewTrait, CookieTrait, SessionTrait;

    public function index(): void {
        /** @var Cards */
        $card = (new CardsModel())->first_where(['id' => 26068]);
        /** @var Cardidentifiers */
        $identifier = (new CardidentifiersModel())->select_where_first(['uuid' => $card->uuid()]);
        /** @var int */
        $health = $this->session->get('health', 20);

        $this->view('pages/index', [
            'card' => $card,
            'identifier' => $identifier,
            'health' => $health,
            'tokens' => $this->get_tokens(),
        ]);
    }

    public function read_deck(): void {
        $content = $this->request->get_file_content('deck');
        $lines = array_map('trim', explode("\n", $content));

        $main_deck = [];
        $side_deck = [];
        $parsing_side_deck = false;

        foreach ($lines as $line) {
            if (empty($line)) {
                $parsing_side_deck = true;
                continue;
            }

            $pattern = '/^(\d+)\s+(.+)$/';
            if (preg_match($pattern, $line, $matches)) {
                $card = [
                    'quantity' => (int) $matches[1],
                    'name' => trim($matches[2])
                ];

                if ($parsing_side_deck) {
                    $side_deck[] = $card;
                } else {
                    $main_deck[] = $card;
                }
            }
        }

        $card_names = array_map(fn($card) => $card['name'], $main_deck);
        $card_quantities = array_map(fn($card) => $card['quantity'], $main_deck);
        $side_card_names = array_map(fn($card) => $card['name'], $side_deck);
        $side_card_quantities = array_map(fn($card) => $card['quantity'], $side_deck);

        $cards = $this->get_cards_from_card_names($card_names, $card_quantities);
        $side_cards = $this->get_cards_from_card_names($side_card_names, $side_card_quantities);

        // $count = count($card_names);
        // for ($i = 0; $i < $count; $i++) {
        //     log_debug($card_names[$i] . ' ' . $card_quantities[$i]);
        // }

        // this should stay for a week methinks
        $this->session->set('deck', $cards);
        $this->session->set('sideboard', $side_cards);
        $this->session->set('__deck__', $cards);
        $this->session->set('__sideboard__', $side_cards);

        // array_map(function (array $card): void {
        //     $this->view('card', [
        //         'multiverse_id' => $card[1]->multiverse_id(),
        //         'alt_text' => $card[0]->name()
        //     ]);
        // }, $cards);
    }

    public function draw(): void {
        $deck = $this->session->get('deck', []);
        if (empty($deck) || count($deck) === 0) {
            return;
        }
        $card = array_shift($deck);
        $this->session->set('deck', $deck);

        if ($card === null || $card[0] === null || $card[1] === null)
            // $this->redirect('/error', [new \Exception('deck is empty')]);
            return;

        // $card[0] = Cards::from_imcomplete_class($card[0]);
        // $card[1] = Cardidentifiers::from_imcomplete_class($card[1]);

        $this->view('card', [
            'card' => $card[0],
            'identifier' => $card[1],
        ]);
    }

    public function reset(): void {
        $this->session->set('deck', $this->session->get('__deck__', []));
        $this->session->set('sideboard', $this->session->get('__sideboard__', []));
        $this->session->set('health', 20);
    }

    private function get_cards_from_card_names(array $card_names, array $card_quantities): array {
        Database::connect();
        $escaped_card_names = array_map([Database::class, 'escape'], $card_names);

        $where_clauses = [];
        foreach ($escaped_card_names as $card_name) {
            $where_clauses[] = "cards.name = '" . $card_name . "'";
        }
        $where_clauses = implode(" OR ", $where_clauses);
        $sql = "SELECT `cards`.*, `cardidentifiers`.*
            FROM cards
            JOIN cardidentifiers ON cards.uuid = cardidentifiers.uuid
            WHERE ($where_clauses)
                AND `cards`.`language` = 'English'
                AND `cardidentifiers`.`multiverseId` IS NOT NULL
            GROUP BY `cards`.`name`";

        $exact_result = Database::query($sql)->get_result()->fetch_all(MYSQLI_ASSOC);
        $exact_cards = array_map(fn(array $row) => [new Cards($row), new Cardidentifiers($row)], $exact_result);
        $matched_names = array_map(fn($card) => $card[0]->name(), $exact_cards);
        $remaining_card_names = array_diff($escaped_card_names, $matched_names);

        // $cards = $exact_cards;
        $cards = [];
        foreach ($matched_names as $card_name) {
            $index = array_search($card_name, $card_names);
            $quantity = $card_quantities[$index];
            $matching_cards = array_filter($exact_cards, fn($card) => $card[0]->name() === $card_name);
            $card = reset($matching_cards);
            for ($i = 0; $i < $quantity; $i++) {
                $cards[] = $card;
            }
        }

        if (!empty($remaining_card_names)) {
            $like_where_clauses = [];
            foreach ($remaining_card_names as $card_name) {
                $like_where_clauses[] = "`cards`.`name` LIKE '%" . $card_name . "%'";
            }
            $like_where_clauses = implode(" OR ", $like_where_clauses);

            $like_sql = "SELECT `cards`.*, `cardidentifiers`.*
                FROM `cards`
                JOIN `cardidentifiers` ON `cards`.`uuid` = `cardidentifiers`.`uuid`
                WHERE ($like_where_clauses)
                    AND `cards`.`language` = 'English'
                    AND `cardidentifiers`.`multiverseId` IS NOT NULL
                GROUP BY `cards`.`name`";

            $like_result = Database::query($like_sql)->get_result()->fetch_all(MYSQLI_ASSOC);
            $like_cards = array_map(fn(array $row) => [new Cards($row), new Cardidentifiers($row)], $like_result);

            foreach ($remaining_card_names as $card_name) {
                $index = array_search($card_name, $card_names);
                $quantity = $card_quantities[$index];

                $matching_cards = array_filter($like_cards, function ($card) use ($card_name): bool {
                    return stripos($card[0]->name(), Database::escape($card_name)) !== false;
                });

                $card = reset($matching_cards);
                for ($i = 0; $i < $quantity; $i++) {
                    $cards[] = $card;
                }
            }
        }

        return $cards;
    }

    public function shuffle(): void {
        $deck = $this->session->get('deck', []);
        shuffle($deck);
        $this->session->set('deck', $deck);
    }

    public function life_decrease() {
        $health = $this->session->get('health', 20) - 1;
        $this->session->set('health', $health);
        $this->view('life', ['health' => $health]);
    }

    public function life_increase() {
        $health = $this->session->get('health', 20) + 1;
        $this->session->set('health', $health);
        $this->view('life', ['health' => $health]);
    }

    public function to_top_deck(string $uuid): void {
        $deck = $this->session->get('deck');
        $og_deck = $this->session->get('__deck__');

        $card = null;
        foreach ($og_deck as $card_data) {
            if ($card_data[0]->uuid() === $uuid) {
                $card = $card_data;
                break;
            }
        }

        if ($card === null)
            return;

        array_unshift($deck, $card);
        $this->session->set('deck', $deck);
    }

    public function to_bottom_deck(string $uuid): void {
        $deck = $this->session->get('deck');
        $og_deck = $this->session->get('__deck__');

        $card = null;
        foreach ($og_deck as $card_data) {
            if ($card_data[0]->uuid() === $uuid) {
                $card = $card_data;
                break;
            }
        }

        if ($card === null)
            return;

        array_push($deck, $card);
        $this->session->set('deck', $deck);
    }

    private function get_tokens(): array {
        $tokens_model = new TokensModel();
        $token_identifiers_model = new TokenidentifiersModel();

        $tokens_table = $tokens_model->get_table();
        $token_identifiers_table = $token_identifiers_model->get_table();
        $tokens = (new QueryBuilder())
            ->select([
                '`tokens`.`uuid`',
                '`tokens`.`power`',
                '`tokens`.`toughness`',
                '`tokenidentifiers`.`multiverseId`'
            ])
            ->from($tokens_table)
            ->join($token_identifiers_table, "`$tokens_table`.`uuid`", QueryOperators::Equals, "`$token_identifiers_table`.`uuid`")
            ->where('`tokenidentifiers`.`multiverseId`', QueryOperators::NotEquals, "''")
            ->fetch_all();

        $result = [];
        foreach ($tokens as $token) {
            $result[] = [
                0 => new Tokens($token),
                1 => new Tokenidentifiers($token),
            ];
        }

        return $result;
    }

    public function token(): void {
        $token_uuid = '7c4596d5-af5c-5e16-802e-38e3cee1fdbc';

        $token = (new TokensModel())->first_where([
            'uuid' => $token_uuid
        ]);
        $identifier = (new TokenidentifiersModel())->first_where([
            'uuid' => $token_uuid
        ]);

        log_info(print_r([$token, $identifier], true));

        $this->view('token', [
            'token' => $token,
            'identifier' => $identifier,
        ]);
    }

    public function search(): void {
        $cards_left_in_deck = [];

        $original_deck = $this->session->get('__deck__');
        $used_deck = $this->session->get('deck');

        $used_quantities = [];
        foreach ($used_deck as $data) {
            if (!$data[0])
                continue;

            $uuid = $data[0]->uuid();
            if (!isset($used_quantities[$uuid])) {
                $used_quantities[$uuid] = 0;
            }
            $used_quantities[$uuid]++;
        }

        $unique_cards = [];
        foreach ($original_deck as $data) {
            if (!isset($data[0], $data[1]))
                continue;
            if (!in_array($data, $unique_cards))
                $unique_cards[] = $data;
        }

        $cards_needed = array_filter($unique_cards, fn($data) => $data[0] && isset ($used_quantities[$data[0]->uuid()]));

        array_walk($cards_needed, function ($data) use (&$cards_left_in_deck, $used_quantities) {
            for ($i = 0; $i < $used_quantities[$data[0]->uuid()]; $i++) {
                $cards_left_in_deck[] = $data;
            }
        });

        $this->view('search', [
            'cards' => $cards_left_in_deck
        ]);
    }

    public function remove_card_from_deck(): void {
        $uuid = $this->request->get_post('uuid');
        $deck = $this->session->get('deck');

        foreach ($deck as $key => $card_data) {
            if (!isset($card_data[0]) || $card_data[0]->uuid() !== $uuid)
                continue;

            log_debug("removing $key for {$card_data[0]->uuid()}");
            unset($deck[$key]);
            break;
        }

        $this->session->set('deck', $deck);
    }
}
