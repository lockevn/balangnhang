using System;
using System.Collections;
using System.Xml.Serialization;

using GURUCORE.Framework.Core.Data;

namespace Quantum.Tala.Service.DTO
{
	/// <summary>
	/// Summary for userDTO.
	/// </summary>
	[Serializable]
	[System.Diagnostics.DebuggerStepThrough]
	[PersistenceClass("security_user","id",true)]
	 public sealed partial class userDTO : DTOBase
	{
		public const string ID_FLD = "id";
		public const string U_FLD = "u";
		public const string PASSWORD_FLD = "password";
		public const string TOKENSTRING_FLD = "tokenstring";
		public const string USERTYPE_FLD = "usertype";

		private int m_nid;
		private string m_su;
		private string m_spassword;
		private string m_stokenstring;
		private int m_nusertype;

		public userDTO()
		{
		}
		
		[PersistenceProperty("id",true,true)]
		public int id
		{
			get
			{
				return m_nid;
			}
			set
			{
				m_htIsNull["id"] = false;
				m_nid = value;
			}
		}
		
		[PersistenceProperty("u",false,false)]
		public string u
		{
			get
			{
				return m_su;
			}
			set
			{
				m_htIsNull["u"] = false;
				m_su = value;
			}
		}
		
		[PersistenceProperty("password",false,false)]
		public string password
		{
			get
			{
				return m_spassword;
			}
			set
			{
				m_htIsNull["password"] = false;
				m_spassword = value;
			}
		}
		
		[PersistenceProperty("tokenstring",false,false)]
		public string tokenstring
		{
			get
			{
				return m_stokenstring;
			}
			set
			{
				m_htIsNull["tokenstring"] = false;
				m_stokenstring = value;
			}
		}
		
		[PersistenceProperty("usertype",false,false)]
		public int usertype
		{
			get
			{
				return m_nusertype;
			}
			set
			{
				m_htIsNull["usertype"] = false;
				m_nusertype = value;
			}
		}

		public string IsNullSerializedString
		{
			get
			{
				return GetIsNullSerializedString();
			}
			set
			{
				SetIsNullSerializedString(value);
			}
		}
	}
}
