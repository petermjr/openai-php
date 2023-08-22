<?php

declare(strict_types=1);

namespace OpenAI\Responses\Files;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Contracts\ResponseHasMetaInformationContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Responses\Concerns\HasMetaInformation;
use OpenAI\Responses\Meta\MetaInformation;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{object: string, data: array<int, array{id: string, object: string, created_at: int, bytes: int, filename: string, purpose: string, status: string, status_details: array<array-key, mixed>|string|null}>}>
 */
final class ListResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /**
     * @use ArrayAccessible<array{object: string, data: array<int, array{id: string, object: string, created_at: int, bytes: int, filename: string, purpose: string, status: string, status_details: array<array-key, mixed>|string|null}>}>
     */
    use ArrayAccessible;

    use HasMetaInformation;
    use Fakeable;

    /**
     * @param  array<int, RetrieveResponse>  $data
     */
    private function __construct(
        public readonly string $object,
        public readonly array $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{object: string, data: array<int, array{id: string, object: string, created_at: int, bytes: int, filename: string, purpose: string, status: string, status_details: array<array-key, mixed>|string|null}>}  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $data = array_map(fn (array $result): RetrieveResponse => RetrieveResponse::from(
            $result,
            $meta,
        ), $attributes['data']);

        return new self(
            $attributes['object'],
            $data,
            $meta,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'object' => $this->object,
            'data' => array_map(
                static fn (RetrieveResponse $response): array => $response->toArray(),
                $this->data,
            ),
        ];
    }
}
