<?php

namespace App\Routes;

use App\Classes\Helper;
use App\Classes\Database;
use App\Classes\Api;
use App\Classes\Response as ResponseBuilder;
use DateTime;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EventController
{

    private $db;
    private $helper;
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->helper = new Helper();
        $this->db = $this->container->get('Database');
    }

    public function get(Request $request, Response $response, array $args): Response
    {

        try {

            $api = new Api($this->db, $request);
            $prevPageUrl = $api->getPrevPageUrl();
            $nextPageUrl = $api->getNextPageUrl();

            $list = $api->getWithPaginator('SELECT * FROM events');
            $maxPages = $api->getMaxPages('events');

            $jsonResponse = ResponseBuilder::build(ResponseBuilder::SUCCESS_RESPONSE_VAL, $list, $prevPageUrl, $nextPageUrl, $maxPages);

        } catch (\Throwable $th) {

            $jsonResponse = ResponseBuilder::build(ResponseBuilder::ERROR_RESPONSE_KEY, [
                ResponseBuilder::CODE_RESPONSE_KEY => $th->getCode(),
                ResponseBuilder::MSG_RESPONSE_KEY => $th->getMessage()
            ]);

        } finally {

            $response->getBody()->write($jsonResponse);

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        }

    }

    public function add(Request $request, Response $response, array $args): Response
    {

        try {

            $params = $request->getQueryParams();
            $now = new DateTime();

            $guidv4 = $this->helper->guidv4();
            $name = $params["name"];
            $description = $params["description"];
            $startAt = $params["start_at"];
            $endAt = $params["end_at"];
            $lat = $params["lat"];
            $lng = $params["lng"];
            $createdAt = $now->format('Y-m-d H:i:s');

            $insertId = $this->db->insert("events",
                ["id", "name", "description", "start_at", "end_at", "lat", "lng", "created_at"],
                [$guidv4, $name, $description, $startAt, $endAt, $lat, $lng, $createdAt]
            );

            $guidv4Throwback = $this->helper->guidv4();
            $insertThrowbackId = $this->db->insert("throwbacks",
                ["id", "description", "social_media_video_url", "events_id", "created_at"],
                [$guidv4Throwback, "", "", $guidv4, $createdAt]
            );

            $jsonResponse = ResponseBuilder::build(ResponseBuilder::SUCCESS_RESPONSE_VAL, [
                ResponseBuilder::INSERT_ID_RESPONSE_KEY => $insertId,
            ]);

        } catch (\Throwable $th) {

            $jsonResponse = ResponseBuilder::build(ResponseBuilder::ERROR_RESPONSE_KEY, [
                ResponseBuilder::CODE_RESPONSE_KEY => $th->getCode(),
                ResponseBuilder::MSG_RESPONSE_KEY => $th->getMessage(),
            ]);

        } finally {

            $response->getBody()->write($jsonResponse);

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        }

    }

    public function edit(Request $request, Response $response, array $args): Response
    {

        try {

            $params = $request->getQueryParams();

            $id = $args['id'];
            $name = $params["name"];
            $description = $params["description"];
            $startAt = $params["start_at"];
            $endAt = $params["end_at"];
            $lat = $params["lat"];
            $lng = $params["lng"];

            $updateArray = [
                "name" => $name,
                "description" => $description,
                "start_at" => $startAt,
                "end_at" => $endAt,
                "lat" => $lat,
                "lng" => $lng
            ];

            $updateId = $this->db->update("events", $id, $updateArray);

            $jsonResponse = ResponseBuilder::build(ResponseBuilder::SUCCESS_RESPONSE_VAL, [
                ResponseBuilder::UPDATE_ID_RESPONSE_KEY => $updateId,
            ]);

        } catch (\Throwable $th) {

            $jsonResponse = ResponseBuilder::build(ResponseBuilder::ERROR_RESPONSE_KEY, [
                ResponseBuilder::CODE_RESPONSE_KEY => $th->getCode(),
                ResponseBuilder::MSG_RESPONSE_KEY => $th->getMessage(),
            ]);

        } finally {

            $response->getBody()->write($jsonResponse);

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        }

    }

}
