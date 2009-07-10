using System;

using Alphanamict.WAF.DataAccess.ORMapping;

namespace Alphanamict.WAF.CodeGenerator.Result
{
	/// <summary>
	/// Summary description for UserDTO.
	/// </summary>
	[PersistenceClass("TblUser","UserID",true)]
	public class UserDTO : DTO
	{
		public const string USERID_FLD = "UserID";
		public const string USERNAME_FLD = "Username";
		public const string PASSWORD_FLD = "Password";
		public const string SYSTEM_FLD = "System";

		private int m_nUserID;
		private string m_sUsername;
		private string m_sPassword;
		private bool m_bSystem;

		public UserDTO()
		{
		}
		
		[PersistenceProperty("UserID",true,true)]
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
		
		[PersistenceProperty("Username",false,false)]
		public string Username
		{
			get
			{
				return m_sUsername;
			}
			set
			{
				m_sUsername = value;
			}
		}
		
		[PersistenceProperty("Password",false,false)]
		public string Password
		{
			get
			{
				return m_sPassword;
			}
			set
			{
				m_sPassword = value;
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
