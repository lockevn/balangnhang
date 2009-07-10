using System;

using Alphanamict.WAF.DataAccess.ORMapping;

namespace Alphanamict.WAF.CodeGenerator.Result
{
	/// <summary>
	/// Summary description for PageItemDTO.
	/// </summary>
	[PersistenceClass("TblPageItem","PageItemID",true)]
	public class PageItemDTO : DTO
	{
		public const string PAGEITEMID_FLD = "PageItemID";
		public const string ITEMNAME_FLD = "ItemName";
		public const string PAGELETID_FLD = "PageletID";
		public const string PERMISSIONID_FLD = "PermissionID";

		private int m_nPageItemID;
		private string m_sItemName;
		private int m_nPageletID;
		private int m_nPermissionID;

		public PageItemDTO()
		{
		}
		
		[PersistenceProperty("PageItemID",true,true)]
		public int PageItemID
		{
			get
			{
				return m_nPageItemID;
			}
			set
			{
				m_nPageItemID = value;
			}
		}
		
		[PersistenceProperty("ItemName",false,false)]
		public string ItemName
		{
			get
			{
				return m_sItemName;
			}
			set
			{
				m_sItemName = value;
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
		
		[PersistenceProperty("PermissionID",false,false)]
		public int PermissionID
		{
			get
			{
				return m_nPermissionID;
			}
			set
			{
				m_nPermissionID = value;
			}
		}
	}
}
