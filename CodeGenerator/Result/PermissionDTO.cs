using System;

using Alphanamict.WAF.DataAccess.ORMapping;

namespace Alphanamict.WAF.CodeGenerator.Result
{
	/// <summary>
	/// Summary description for PermissionDTO.
	/// </summary>
	[PersistenceClass("TblPermission","PermissionID",true)]
	public class PermissionDTO : DTO
	{
		public const string PERMISSIONID_FLD = "PermissionID";
		public const string PERMISSIONNAME_FLD = "PermissionName";
		public const string DESCRIPTION_FLD = "Description";
		public const string PARENTPERMISSIONID_FLD = "ParentPermissionID";

		private int m_nPermissionID;
		private string m_sPermissionName;
		private string m_sDescription;
		private int m_nParentPermissionID;

		public PermissionDTO()
		{
		}
		
		[PersistenceProperty("PermissionID",true,true)]
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
		
		[PersistenceProperty("PermissionName",false,false)]
		public string PermissionName
		{
			get
			{
				return m_sPermissionName;
			}
			set
			{
				m_sPermissionName = value;
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
		
		[PersistenceProperty("ParentPermissionID",false,false)]
		public int ParentPermissionID
		{
			get
			{
				return m_nParentPermissionID;
			}
			set
			{
				m_nParentPermissionID = value;
			}
		}
	}
}
