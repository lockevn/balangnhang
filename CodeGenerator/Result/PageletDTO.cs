using System;

using Alphanamict.WAF.DataAccess.ORMapping;

namespace Alphanamict.WAF.CodeGenerator.Result
{
	/// <summary>
	/// Summary description for PageletDTO.
	/// </summary>
	[PersistenceClass("TblPagelet","PageletID",true)]
	public class PageletDTO : DTO
	{
		public const string PAGELETID_FLD = "PageletID";
		public const string TITLE_FLD = "Title";
		public const string PATH_FLD = "Path";
		public const string PAGEID_FLD = "PageID";
		public const string ZONE_FLD = "Zone";

		private int m_nPageletID;
		private string m_sTitle;
		private string m_sPath;
		private int m_nPageID;
		private string m_sZone;

		public PageletDTO()
		{
		}
		
		[PersistenceProperty("PageletID",true,true)]
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
		
		[PersistenceProperty("Title",false,false)]
		public string Title
		{
			get
			{
				return m_sTitle;
			}
			set
			{
				m_sTitle = value;
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
	}
}
