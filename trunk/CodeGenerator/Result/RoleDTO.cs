using System;

using Alphanamict.WAF.DataAccess.ORMapping;

namespace Alphanamict.WAF.CodeGenerator.Result
{
	/// <summary>
	/// Summary description for RoleDTO.
	/// </summary>
	[PersistenceClass("TblRole","RoleID",true)]
	public class RoleDTO : DTO
	{
		public const string ROLEID_FLD = "RoleID";
		public const string ROLENAME_FLD = "RoleName";
		public const string DESCRIPTION_FLD = "Description";
		public const string PARENTROLEID_FLD = "ParentRoleID";
		public const string SYSTEM_FLD = "System";

		private int m_nRoleID;
		private string m_sRoleName;
		private string m_sDescription;
		private int m_nParentRoleID;
		private bool m_bSystem;

		public RoleDTO()
		{
		}
		
		[PersistenceProperty("RoleID",true,true)]
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
		
		[PersistenceProperty("RoleName",false,false)]
		public string RoleName
		{
			get
			{
				return m_sRoleName;
			}
			set
			{
				m_sRoleName = value;
			}
		}
		
		[PersistenceProperty("Description",false,false)]
		public string Description
		{
			get
			{
				return m_sDescription;
			}
			set
			{
				m_sDescription = value;
			}
		}
		
		[PersistenceProperty("ParentRoleID",false,false)]
		public int ParentRoleID
		{
			get
			{
				return m_nParentRoleID;
			}
			set
			{
				m_nParentRoleID = value;
			}
		}
		
		[PersistenceProperty("System",false,false)]
		public bool System
		{
			get
			{
				return m_bSystem;
			}
			set
			{
				m_bSystem = value;
			}
		}
	}
}
