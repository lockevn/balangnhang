using System;

using Alphanamict.WAF.DataAccess.ORMapping;

namespace Alphanamict.WAF.CodeGenerator.Result
{
	/// <summary>
	/// Summary description for RolePermissionDTO.
	/// </summary>
	[PersistenceClass("TblRolePermission","RoleRightID",true)]
	public class RolePermissionDTO : DTO
	{
		public const string ROLERIGHTID_FLD = "RoleRightID";
		public const string ROLEID_FLD = "RoleID";
		public const string PERMISSIONID_FLD = "PermissionID";
		public const string PAGELETID_FLD = "PageletID";

		private int m_nRoleRightID;
		private int m_nRoleID;
		private int m_nPermissionID;
		private int m_nPageletID;

		public RolePermissionDTO()
		{
		}
		
		[PersistenceProperty("RoleRightID",true,true)]
		public int RoleRightID
		{
			get
			{
				return m_nRoleRightID;
			}
			set
			{
				m_nRoleRightID = value;
			}
		}
		
		[PersistenceProperty("RoleID",false,false)]
		public int RoleID
		{
			get
			{
				return m_nRoleID;
			}
			set
			{
				m_nRoleID = value;
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
	}
}
