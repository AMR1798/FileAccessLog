<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: AMRMZKR <ammar.muzakkir@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\FALog\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCA\Files_Sharing\Event\ShareLinkAccessedEvent;
use OCA\FALog\Listener\ShareLinkAccessedListener;
use OCP\EventDispatcher\IEventDispatcher;
use OCA\Viewer\Event\LoadViewer;
use OCP\Util;
use OCP\Preview\BeforePreviewFetchedEvent;
use OC\Files\Filesystem;

use OCA\FALog\Actions\Files;


use function OCP\Log\logger;
// use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
use OCA\Files_Sharing\Event\BeforeTemplateRenderedEvent;
use OCA\DAV\Events\SabrePluginAddEvent;
use \OCP\Files\Events\FileScannedEvent;



class Application extends App {
	public const APP_ID = 'falog';

	public function __construct() {
		parent::__construct(self::APP_ID);
		/* @var IEventDispatcher $eventDispatcher */
		$dispatcher = $this->getContainer()->get(IEventDispatcher::class);
        $dispatcher->addServiceListener(ShareLinkAccessedEvent::class, ShareLinkAccessedListener::class);
		$dispatcher->addListener(\OCP\Files\Node::class, function(\OCP\Files\Node $event) {
			// logger('core')->warning("HELLO NODE");
		});
		// $dispatcher->addServiceListener(NodeTouchedEvent::class, function(NodeTouchedEvent $event) {
		// 	var_dump("HELLO");
		// });
		$dispatcher->addListener(SabrePluginAddEvent::class, function(SabrePluginAddEvent $event) {
			// var_dump($event->isSuccessful(), $event->getPath());
			// logger('core')->warning("HELLO SABRE");
		});
		// $dispatcher->addServiceListener(LoadAdditionalScriptsEvent::class, function(LoadAdditionalScriptsEvent $event) {
		// 	var_dump("RENDERED", $event);
		// });
		$dispatcher->addListener(BeforeTemplateRenderedEvent::class, function(BeforeTemplateRenderedEvent $event) {
			if (!$event instanceof BeforeTemplateRenderedEvent) {
				return;
			}
			// Util::addScript(Application::APP_ID, 'falog-test', 'core');
			// Util::addScript(Application::APP_ID, 'test', 'core');
			// logger('core')->warning("HELLO WORLD2");
		});

		$dispatcher->addListener(FileScannedEvent::class, function(FileScannedEvent $event){
			// logger('core')->warning("HELLO FILE", ['path' => $event->getAbsolutePath()]);
		});

		$dispatcher->addListener(LoadViewer::class, function(LoadViewer $event) {
			if (!$event instanceof LoadViewer) {
				return;
			}
			// Util::addScript(Application::APP_ID, 'falog-test', 'core');
			// Util::addScript(Application::APP_ID, 'test', 'core');
			// Util::addScript(Application::APP_ID, 'test', 'files');
			// logger('core')->warning("HELLO WORLD1");
		});

		$dispatcher->addListener(
			BeforePreviewFetchedEvent::class,
			function (BeforePreviewFetchedEvent $event) {
				// logger('core')->warning("HELLO WORLD4");
			}
		);
		$entah = new Files(logger('core'));
		Util::connectHook(
			Filesystem::CLASSNAME,
			Filesystem::signal_read,
			$entah,
			'read'
		);
	}
}

// 	// public function register(IRegistrationContext $context): void {
// 	// 	$context->registerEventListener(ShareLinkAccessedEvent::class, ShareLinkAccessedListener::class);

// 	// 	$context->registerService(IFunctionProvider::class, function () {
// 	// 		return \OC::$server->get(FunctionProvider::class);
// 	// 	});
// 	// }
// }
