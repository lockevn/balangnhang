using System;

using Alphanamict.WAF.DataAccess.ORMapping;

namespace Alphanamict.WAF.CodeGenerator.Result
{
	/// <summary>
	/// Summary description for PageDTO.
	/// </summary>
	[PersistenceClass("TblPage","PageID",true)]
	public class PageDTO : DTO
	{
		public const string PAGEID_FLD = "PageID";
		public const string TITLE_FLD = "Title";
		public const string URL_FLD = "URL";
		public const string LAYOUTID_FLD = "LayoutID";

		private int m_nPageID;
		private string m_sTitle;
		private string m_sURL;
		private int m_nLayoutID;

		public PageDTO()
		{
		}
		
		[PersistenceProperty("PageID",true,true)]
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
	}
}
