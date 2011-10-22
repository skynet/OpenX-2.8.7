/*
+---------------------------------------------------------------------------+
| OpenX v2.8                                                                |
| =========                                                                 |
|                                                                           |
| Copyright (c) 2003-2010 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU Lesser General Public License as published  |
| by the Free Software Foundation; either version 2 of the License, or      |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU Lesser General Public License for more details.                       |
|                                                                           |
| You should have received a copy of the GNU Lesser General Public License  |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id:$
*/

package org.openads.proxy;
import java.util.Date;
import java.util.Map;

import org.apache.xmlrpc.XmlRpcException;
import org.apache.xmlrpc.client.XmlRpcClient;

/**
 * Base class for all banner web service tests.
 *
 */
class BannerService extends AbstractService{
	private static final String BANNER_SERVICE = "BannerXmlRpcService.php";

	private static final String ADD_BANNER_METHOD = "addBanner";
	private static final String DELETE_BANNER_METHOD = "deleteBanner";
	private static final String MODIFY_BANNER_METHOD = "modifyBanner";
	private static final String GET_BANNER_LIST_BY_CAMPAIGN_ID_METHOD = "getBannerListByCampaignId";
	private static final String GET_BANNER_METHOD = "getBanner";
	private static final String BANNER_DAILY_STATISTICS_METHOD = "bannerDailyStatistics";
	private static final String BANNER_PUBLISHER_STATISTICS_METHOD = "bannerPublisherStatistics";
	private static final String BANNER_ZONE_STATISTICS_METHOD = "bannerZoneStatistics";

	/**
	 * Instantiates a new banner service.
	 *
	 * @param client the client
	 * @param basepath the basepath
	 */
	public BannerService(XmlRpcClient client, String basepath) {
		super(client, basepath);
	}

	/* (non-Javadoc)
	 * @see org.openads.proxy.AbstractService#getService()
	 */
	@Override
	String getService() {
		return BANNER_SERVICE;
	}

	/**
	 * Adds the banner.
	 *
	 * @param params the params
	 *
	 * @return the integer
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Integer addBanner(Map params) throws XmlRpcException {
		return (Integer) execute(ADD_BANNER_METHOD, params);
	}

	/**
	 * Modify banner.
	 *
	 * @param params the params
	 *
	 * @return the boolean
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Boolean modifyBanner(Map params) throws XmlRpcException {
		return (Boolean) execute(MODIFY_BANNER_METHOD, params);
	}

	/**
	 * Delete banner.
	 *
	 * @param id the id
	 *
	 * @return the boolean
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Boolean deleteBanner(Integer id) throws XmlRpcException {
		return (Boolean) execute(DELETE_BANNER_METHOD, id);
	}

	/**
	 * Gets the banner.
	 *
	 * @param id the id
	 *
	 * @return the banner
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Map getBanner(Integer id) throws XmlRpcException {
		return (Map) execute(GET_BANNER_METHOD, id);
	}

	/**
	 * Gets the banner list by advertiser id.
	 *
	 * @param id the id
	 *
	 * @return the banner list by advertiser id
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Map[] getBannerListByAdvertiserID(Integer id) throws XmlRpcException {
		return objectToArrayMaps(execute(GET_BANNER_LIST_BY_CAMPAIGN_ID_METHOD, id));
	}

	/**
	 * Banner daily statistics.
	 *
	 * @param id the id
	 *
	 * @return the Map[]
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Map[] bannerDailyStatistics(Integer id) throws XmlRpcException {
		return objectToArrayMaps( execute(BANNER_DAILY_STATISTICS_METHOD, id));
	}

	/**
	 * Banner daily statistics.
	 *
	 * @param id the id
	 * @param startDate the start date
	 *
	 * @return the Map[]
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Map[] bannerDailyStatistics(Integer id, Date startDate)
			throws XmlRpcException {
		return objectToArrayMaps( execute(BANNER_DAILY_STATISTICS_METHOD, id, startDate));
	}

	/**
	 * Banner daily statistics.
	 *
	 * @param id the id
	 * @param startDate the start date
	 * @param endDate the end date
	 *
	 * @return the Map[]
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Map[] bannerDailyStatistics(Integer id, Date startDate,
			Date endDate) throws XmlRpcException {
		return objectToArrayMaps( execute(BANNER_DAILY_STATISTICS_METHOD, id, startDate,
				endDate));
	}

	/**
	 * Banner publisher statistics.
	 *
	 * @param id the id
	 *
	 * @return the Map[]
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Map[] bannerPublisherStatistics(Integer id) throws XmlRpcException {
		return objectToArrayMaps( execute(BANNER_PUBLISHER_STATISTICS_METHOD, id));
	}

	/**
	 * Banner publisher statistics.
	 *
	 * @param id the id
	 * @param startDate the start date
	 *
	 * @return the Map[]
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Map[] bannerPublisherStatistics(Integer id, Date startDate)
			throws XmlRpcException {
		return objectToArrayMaps( execute(BANNER_PUBLISHER_STATISTICS_METHOD, id, startDate));
	}

	/**
	 * Banner publisher statistics.
	 *
	 * @param id the id
	 * @param startDate the start date
	 * @param endDate the end date
	 *
	 * @return the Map[]
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Map[] bannerPublisherStatistics(Integer id, Date startDate,
			Date endDate) throws XmlRpcException {
		return objectToArrayMaps( execute(BANNER_PUBLISHER_STATISTICS_METHOD, id, startDate,
				endDate));
	}

	/**
	 * Banner zone statistics.
	 *
	 * @param id the id
	 *
	 * @return the Map[]
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Map[] bannerZoneStatistics(Integer id) throws XmlRpcException {
		return objectToArrayMaps( execute(BANNER_ZONE_STATISTICS_METHOD, id));
	}

	/**
	 * Banner zone statistics.
	 *
	 * @param id the id
	 * @param startDate the start date
	 *
	 * @return the Map[]
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Map[] bannerZoneStatistics(Integer id, Date startDate)
			throws XmlRpcException {
		return objectToArrayMaps( execute(BANNER_ZONE_STATISTICS_METHOD, id, startDate));
	}

	/**
	 * Banner zone statistics.
	 *
	 * @param id the id
	 * @param startDate the start date
	 * @param endDate the end date
	 *
	 * @return the Map[]
	 *
	 * @throws XmlRpcException the xml rpc exception
	 */
	public Map[] bannerZoneStatistics(Integer id, Date startDate,
			Date endDate) throws XmlRpcException {
		return objectToArrayMaps( execute(BANNER_ZONE_STATISTICS_METHOD, id, startDate,
				endDate));
	}
}
