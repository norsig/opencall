<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command;

use \PHPUnit_Framework_TestCase as StandardTestCase;

/**
 * @group commands
 * @group realm-set
 */
class SetPopTest extends CommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\SetPop';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'SPOP';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key');
        $expected = array('key');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame('member', $this->getCommand()->parseResponse('member'));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key');
        $expected = array('prefix:key');

        $command = $this->getCommandWithArgumentsArray($arguments);
        $command->prefixKeys('prefix:');

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeysIgnoredOnEmptyArguments()
    {
        $command = $this->getCommand();
        $command->prefixKeys('prefix:');

        $this->assertSame(array(), $command->getArguments());
    }

    /**
     * @group connected
     */
    public function testPopsRandomMemberFromSet()
    {
        $redis = $this->getClient();

        $redis->sadd('letters', 'a', 'b');

        $this->assertContains($redis->spop('letters'), array('a', 'b'));
        $this->assertContains($redis->spop('letters'), array('a', 'b'));

        $this->assertNull($redis->spop('letters'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $redis->spop('foo');
    }
}
