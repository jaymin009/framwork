<?php

namespace Flinnt\Core\View\Widgets\Factories;

/**
 * Class WidgetFactory
 *
 * @package Flinnt\Core\View\Widgets\Factories
 */
class WidgetFactory extends AbstractWidgetFactory
{

	/**
	 * Run widget without magic method.
	 *
	 * @return mixed
	 */
	public function run()
	{
		$args = func_get_args();

		//dd($args);


		$this->instantiateWidget($args);


		$content = $this->getContentFromCache($args);

		if ( $timeout = (float) $this->getReloadTimeout() ) {
			$content .= $this->javascriptFactory->getReloader($timeout);
			$content = $this->wrapContentInContainer($content);
		}


		return $this->convertToViewExpression($content);
	}

	/**
	 * Gets content from cache if it's turned on.
	 * Runs widget class otherwise.
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	protected function getContentFromCache( $args )
	{


		if ( $cacheTime = (float) $this->getCacheTime() ) {

			//var_dump($cacheTime);

			return $this->app->cache($this->widget->cacheKey($args), $cacheTime, function () {
				return $this->getContent();
			});
		}

		return $this->getContent();
	}

	/**
	 * Get widget cache time or false if it's not meant to be cached.
	 *
	 * @return bool|float|int
	 */
	protected function getCacheTime()
	{
		return isset($this->widget) && $this->widget->cacheTime ? $this->widget->cacheTime : false;
	}

	/**
	 * Make call and get return widget content.
	 *
	 * @return mixed
	 */
	protected function getContent()
	{
		$content = $this->app->call([$this->widget, 'run'], $this->widgetParams);

		return is_object($content) ? $content->__toString() : $content;
	}

	/**
	 * Get widget reload timeout or false if it's not reloadable.
	 *
	 * @return bool|float|int
	 */
	protected function getReloadTimeout()
	{
		return isset($this->widget) && $this->widget->reloadTimeout ? $this->widget->reloadTimeout : false;
	}
}
