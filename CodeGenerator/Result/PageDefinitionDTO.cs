using System;

using Alphanamict.WAF.DataAccess.ORMapping;

namespace Alphanamict.WAF.CodeGenerator.Result
{
	/// <summary>
	/// Summary description for PageDefinitionDTO.
	/// </summary>
	[PersistenceClass("VwPageDefinition",null,false)]
	public class PageDefinitionDTO : DTO
	{
		public const string PAGEID_FLD = "PageID";
		public const string PAGETITLE_FLD = "PageTitle";
		public const string URL_FLD = "URL";
		public const string PAGELETID_FLD = "PageletID";
		public const string PAGELETTITLE_FLD = "PageletTitle";
		public const string PATH_FLD = "Path";
		public const string ZONE_FLD = "Zone";
		public const string LAYOUTID_FLD = "LayoutID";
		public const string LAYOUTNAME_FLD = "LayoutName";

		private int m_nPageID;
		private string m_sPageTitle;
		private string m_sURL;
		private int m_nPageletID;
		private string m_sPageletTitle;
		private string m_sPath;
		private string m_sZone;
		private int m_nLayoutID;
		private string m_sLayoutName;

		public PageDefinitionDTO()
		{
		}
		
		[PersistenceProperty("PageID",false,false)]
		public int PageID
		{
			get
			{
				return m_nPageID;
			}
			set
			{
				m_nPageID = value;
			}
		}
		
		[PersistenceProperty("PageTitle",false,false)]
		public string PageTitle
		{
			get
			{
				return m_sPageTitle;
			}
			set
			{
				m_sPageTitle = value;
			}
		}
		
		[PersistenceProperty("URL",false,false)]
		public string URL
		{
			get
			{
				return m_sURL;
			}
			set
			{
				m_sURL = value;
			}
		}
		
		[PersistenceProperty("PageletID",false,false)]
		public int PageletID
		{
			get
			{
				return m_nPageletID;
			}
			set
			{
				m_nPageletID = value;
			}
		}
		
		[PersistenceProperty("PageletTitle",false,false)]
		public string PageletTitle
		{
			get
			{
				return m_sPageletTitle;
			}
			set
			{
				m_sPageletTitle = value;
			}
		}
		
		[PersistenceProperty("Path",false,false)]
		public string Path
		{
			get
			{
				return m_sPath;
			}
			set
			{
				m_sPath = value;
			}
		}
		
		[PersistenceProperty("Zone",false,false)]
		public string Zone
		{
			get
			{
				return m_sZone;
			}
			set
			{
				m_sZone = value;
			}
		}
		
		[PersistenceProperty("LayoutID",false,false)]
		public int LayoutID
		{
			get
			{
				return m_nLayoutID;
			}
			set
			{
				m_nLayoutID = value;
			}
		}
		
		[PersistenceProperty("LayoutName",false,false)]
		public string LayoutName
		{
			get
			{
				return m_sLayoutName;
			}
			set
			{
				m_sLayoutName = value;
			}
		}
	}
}
