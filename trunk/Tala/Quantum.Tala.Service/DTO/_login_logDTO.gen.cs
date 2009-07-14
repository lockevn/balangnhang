using System;
using System.Collections;
using System.Xml.Serialization;

using GURUCORE.Framework.Core.Data;

namespace Quantum.Tala.Service.DTO
{
	/// <summary>
	/// Summary for login_logDTO.
	/// </summary>
	[Serializable]
	[System.Diagnostics.DebuggerStepThrough]
	[PersistenceClass("security_login_log","id",true)]
	 public sealed partial class login_logDTO : DTOBase
	{
		public const string ID_FLD = "id";
		public const string U_FLD = "u";
		public const string IP_FLD = "ip";
		public const string PROFILE_SNAPSHOT_FLD = "profile_snapshot";
		public const string DT_FLD = "dt";

		private int m_nid;
		private string m_su;
		private string m_sip;
		private string m_sprofile_snapshot;
		private object m_odt;

		public login_logDTO()
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
		
		[PersistenceProperty("ip",false,false)]
		public string ip
		{
			get
			{
				return m_sip;
			}
			set
			{
				m_htIsNull["ip"] = false;
				m_sip = value;
			}
		}
		
		[PersistenceProperty("profile_snapshot",false,false)]
		public string profile_snapshot
		{
			get
			{
				return m_sprofile_snapshot;
			}
			set
			{
				m_htIsNull["profile_snapshot"] = false;
				m_sprofile_snapshot = value;
			}
		}
		
		[PersistenceProperty("dt",false,false)]
		public object dt
		{
			get
			{
				return m_odt;
			}
			set
			{
				m_htIsNull["dt"] = false;
				m_odt = value;
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
