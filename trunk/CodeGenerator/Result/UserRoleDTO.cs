using System;

using Alphanamict.WAF.DataAccess.ORMapping;

namespace Alphanamict.WAF.CodeGenerator.Result
{
	/// <summary>
	/// Summary description for UserRoleDTO.
	/// </summary>
	[PersistenceClass("TblUserRole","UserRoleID",true)]
	public class UserRoleDTO : DTO
	{
		public const string USERROLEID_FLD = "UserRoleID";
		public const string USERID_FLD = "UserID";
		public const string ROLEID_FLD = "RoleID";

		private int m_nUserRoleID;
		private int m_nUserID;
		private int m_nRoleID;

		public UserRoleDTO()
		{
		}
		
		[PersistenceProperty("UserRoleID",true,true)]
		public int UserRoleID
		{
			get
			{
				return m_nUserRoleID;
			}
			set
			{
				m_nUserRoleID = value;
			}
		}
		
		[PersistenceProperty("UserID",false,false)]
		public int UserID
		{
			get
			{
				return m_nUserID;
			}
			set
			{
				m_nUserID = value;
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
	}
}
