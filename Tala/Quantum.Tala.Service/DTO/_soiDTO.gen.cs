using System;
using System.Collections;
using System.Xml.Serialization;

using GURUCORE.Framework.Core.Data;
using MySql.Data.Types;

namespace Quantum.Tala.Service.DTO
{
	/// <summary>
	/// Summary for soiDTO.
	/// </summary>
	[Serializable]
	[System.Diagnostics.DebuggerStepThrough]
	[PersistenceClass("game_soi","id",true)]
	 public sealed partial class soiDTO : DTOBase
	{
		public const string ID_FLD = "id";
		public const string NAME_FLD = "name";
		public const string DESC_FLD = "desc";
		public const string OWNER_FLD = "owner";
		public const string DT_FLD = "dt";
		public const string OPTION_FLD = "option";
		public const string NUMOFVAN_FLD = "numofvan";
		public const string TOURNAMENTID_FLD = "tournamentid";
		public const string ISEND_FLD = "isend";
		public const string STARTTIME_FLD = "starttime";
		public const string ENDTIME_FLD = "endtime";
		

		private int m_nid;
		private string m_sname;
		private string m_sdesc;
		private string m_sowner;
		private MySqlDateTime m_dtdt;
		private string m_soption;
		private int m_nnumofvan;
		private int m_ntournamentid;
		private bool m_bisend;
		private MySqlDateTime m_dtstarttime;
		private MySqlDateTime m_dtendtime;
		
		public soiDTO()
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
		
		[PersistenceProperty("name",false,false)]
		public string name
		{
			get
			{
				return m_sname;
			}
			set
			{
				m_htIsNull["name"] = false;
				m_sname = value;
			}
		}
		
		[PersistenceProperty("desc",false,false)]
		public string desc
		{
			get
			{
				return m_sdesc;
			}
			set
			{
				m_htIsNull["desc"] = false;
				m_sdesc = value;
			}
		}
		
		[PersistenceProperty("owner",false,false)]
		public string owner
		{
			get
			{
				return m_sowner;
			}
			set
			{
				m_htIsNull["owner"] = false;
				m_sowner = value;
			}
		}
		
		[PersistenceProperty("dt",false,false)]
		public MySqlDateTime dt
		{
			get
			{
				return m_dtdt;
			}
			set
			{
				m_htIsNull["dt"] = false;
				m_dtdt = value;
			}
		}
		
		[PersistenceProperty("option",false,false)]
		public string option
		{
			get
			{
				return m_soption;
			}
			set
			{
				m_htIsNull["option"] = false;
				m_soption = value;
			}
		}
		
		[PersistenceProperty("numofvan",false,false)]
		public int numofvan
		{
			get
			{
				return m_nnumofvan;
			}
			set
			{
				m_htIsNull["numofvan"] = false;
				m_nnumofvan = value;
			}
		}
		
		[PersistenceProperty("tournamentid",false,false)]
		public int tournamentid
		{
			get
			{
				return m_ntournamentid;
			}
			set
			{
				m_htIsNull["tournamentid"] = false;
				m_ntournamentid = value;
			}
		}
		
		[PersistenceProperty("isend",false,false)]
		public bool isend
		{
			get
			{
				return m_bisend;
			}
			set
			{
				m_htIsNull["isend"] = false;
				m_bisend = value;
			}
		}
		
		[PersistenceProperty("starttime",false,false)]
		public MySqlDateTime starttime
		{
			get
			{
				return m_dtstarttime;
			}
			set
			{
				m_htIsNull["starttime"] = false;
				m_dtstarttime = value;
			}
		}
		
		[PersistenceProperty("endtime",false,false)]
		public MySqlDateTime endtime
		{
			get
			{
				return m_dtendtime;
			}
			set
			{
				m_htIsNull["endtime"] = false;
				m_dtendtime = value;
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
