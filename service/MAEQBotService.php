<?php

class MAEQBotService
{
    const DIRECTION_NORTH = 'North';
    const DIRECTION_EAST = 'East';
    const DIRECTION_SOUTH = 'South';
    const DIRECTION_WEST = 'West';
    const TURN_LEFT = 'L';
    const TURN_RIGHT = 'R';

    protected array $position;
    protected array $compass = [
        self::DIRECTION_NORTH,
        self::DIRECTION_EAST,
        self::DIRECTION_SOUTH,
        self::DIRECTION_WEST,
    ];

    /**
     * @param int $origin_x
     * @param int $origin_y
     * @param string $direction
     */
    public function __construct(int $origin_x = 0, int $origin_y = 0, string $direction = self::DIRECTION_NORTH)
    {
        $this->position = [
            'x' => $origin_x,
            'y' => $origin_y,
        ];

        $this->turnTo($direction);
    }

    /**
     * @return mixed|string
     */
    public function getDirection(): mixed
    {
        return $this->compass[0];
    }

    /**
     * @return array|int[]
     */
    public function getPosition(): array
    {
        return $this->position;
    }

    /**
     * @throws Exception
     */
    public function walking($route): void
    {
        if ($this->routeValidate(route: $route) === false) {
            throw new Exception(sprintf('Route: %s is invalid', $route));
        }

        $moves = $this->routeSplit(route: $route);

        foreach ($moves as $move) {
            $this->move(move: $move);
        }
    }

    /**
     * @param $route
     * @return bool
     */
    protected function routeValidate($route): bool
    {
        return (bool)preg_match('/^([RL]*W?\d*)+$/', $route);
    }

    /**
     * @param $route
     * @return string[]
     */
    protected function routeSplit($route): array
    {
        preg_match_all('/[WRL]+\d*/', $route, $moves);

        return $moves[0];
    }

    /**
     * @param $move
     * @return void
     */
    protected function move($move): void
    {
        preg_match('/([RL]+)?(W(\d*))?/', $move, $match);
        $directions = $match[1] ?? null;
        $distance = $match[3] ?? 0;

        $this->turn(directions: $directions);
        $this->forward(distance: $distance);
    }

    /**
     * @param $directions
     * @return void
     */
    protected function turn($directions): void
    {
        if (empty($directions)) {
            return;
        }

        $directions = str_split($directions);

        foreach ($directions as $direction) {
            switch ($direction) {
                case self::TURN_LEFT:
                    $turn_to = array_pop($this->compass);
                    array_unshift($this->compass, $turn_to);
                    break;
                case self::TURN_RIGHT:
                    $turn_to = array_shift($this->compass);
                    $this->compass[] = $turn_to;
                    break;
            }
        }
    }

    /**
     * @param $direction
     * @return void
     */
    protected function turnTo($direction): void
    {
        if ($this->getDirection() !== $direction) {
            $this->turn(directions: self::TURN_LEFT);
            $this->turnTo(direction: $direction);
        }
    }

    /**
     * @param $distance
     * @return void
     */
    protected function forward($distance): void
    {
        if ($distance === 0) {
            return;
        }

        match ($this->getDirection()) {
            self::DIRECTION_NORTH, self::DIRECTION_SOUTH => $this->position['y'] += (int)$distance,
            self::DIRECTION_EAST, self::DIRECTION_WEST => $this->position['x'] += (int)$distance,
            default => '',
        };

    }
}